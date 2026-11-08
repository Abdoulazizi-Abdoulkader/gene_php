<?php

declare(strict_types=1);

namespace Gene\Schema;

use PDO;

/**
 * Lit le schéma d'une base MySQL (5.7+, 8.x) ou MariaDB (10.3+) via information_schema.
 * Quatre requêtes au total, quel que soit le nombre de tables.
 */
final class MySqlSchemaReader implements SchemaReader
{
    public function __construct(private readonly PDO $pdo) {}

    /**
     * Construit un DSN à partir de ses composants, en refusant ce qui pourrait y injecter
     * d'autres paramètres (« ; », « = ») et un port non numérique. Sans base, le DSN vise le serveur seul
     * (pour lister les bases).
     */
    public static function dsn(string $host, string $port, string $database = ''): string
    {
        if ($host === '' || preg_match('/[;=\s]/', $host . $database) === 1 || !ctype_digit($port)) {
            throw new \InvalidArgumentException('Hôte, port ou base invalide (« ; », « = » et espaces interdits ; port numérique).');
        }

        return sprintf('mysql:host=%s;port=%s;%scharset=utf8mb4', $host, $port, $database !== '' ? 'dbname=' . $database . ';' : '');
    }

    /**
     * Bases visibles par le compte connecté, hors bases système.
     *
     * @return list<string>
     */
    public static function listDatabases(PDO $pdo): array
    {
        $statement = $pdo->prepare('SHOW DATABASES');
        $statement->execute();
        $system = ['information_schema', 'mysql', 'performance_schema', 'sys'];

        return array_values(array_filter(
            array_map(strval(...), $statement->fetchAll(PDO::FETCH_COLUMN)),
            static fn(string $db): bool => !in_array(strtolower($db), $system, true),
        ));
    }

    /** Ouvre une connexion PDO adaptée à l'introspection (exceptions, UTF-8). */
    public static function connect(string $dsn, string $user, string $password): PDO
    {
        if (!str_starts_with($dsn, 'mysql:')) {
            throw new \InvalidArgumentException('Seuls les DSN MySQL/MariaDB (« mysql:… ») sont pris en charge.');
        }
        if (!str_contains($dsn, 'charset=')) {
            $dsn .= ';charset=utf8mb4';
        }

        return new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    public function read(): Schema
    {
        $database = $this->scalar('SELECT DATABASE()');
        if (!is_string($database) || $database === '') {
            throw new \RuntimeException('Aucune base sélectionnée : ajoutez « dbname=… » au DSN.');
        }
        $version = (string) $this->scalar('SELECT VERSION()');
        $isMariaDb = stripos($version, 'mariadb') !== false;

        $tables = $this->fetch(
            "SELECT TABLE_NAME AS table_name, TABLE_COMMENT AS table_comment
               FROM information_schema.TABLES
              WHERE TABLE_SCHEMA = :db AND TABLE_TYPE = 'BASE TABLE'",
            $database,
        );

        $primary = [];
        foreach ($this->fetch(
            "SELECT TABLE_NAME AS table_name, COLUMN_NAME AS column_name
               FROM information_schema.KEY_COLUMN_USAGE
              WHERE TABLE_SCHEMA = :db AND CONSTRAINT_NAME = 'PRIMARY'",
            $database,
        ) as $row) {
            $primary[$row['table_name']][$row['column_name']] = true;
        }

        $columns = [];
        foreach ($this->fetch(
            'SELECT TABLE_NAME AS table_name, COLUMN_NAME AS column_name, DATA_TYPE AS data_type,
                    COLUMN_TYPE AS column_type, IS_NULLABLE AS is_nullable, COLUMN_DEFAULT AS column_default,
                    EXTRA AS extra, CHARACTER_MAXIMUM_LENGTH AS max_length, NUMERIC_PRECISION AS num_precision,
                    NUMERIC_SCALE AS num_scale, COLUMN_COMMENT AS column_comment
               FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = :db
              ORDER BY TABLE_NAME, ORDINAL_POSITION',
            $database,
        ) as $row) {
            $columns[$row['table_name']][] = self::buildColumn($row, isset($primary[$row['table_name']][$row['column_name']]), $isMariaDb);
        }

        $constraints = [];
        foreach ($this->fetch(
            'SELECT CONSTRAINT_NAME AS constraint_name, TABLE_NAME AS table_name, COLUMN_NAME AS column_name,
                    REFERENCED_TABLE_NAME AS ref_table, REFERENCED_COLUMN_NAME AS ref_column
               FROM information_schema.KEY_COLUMN_USAGE
              WHERE TABLE_SCHEMA = :db AND REFERENCED_TABLE_SCHEMA = :db AND REFERENCED_TABLE_NAME IS NOT NULL
              ORDER BY TABLE_NAME, CONSTRAINT_NAME, ORDINAL_POSITION',
            $database,
        ) as $row) {
            $constraints[$row['table_name']][$row['constraint_name']][] = $row;
        }

        $result = [];
        foreach ($tables as $t) {
            $name = $t['table_name'];
            $fks = [];
            foreach ($constraints[$name] ?? [] as $constraintName => $parts) {
                if (count($parts) === 1) { // les clés étrangères composites ne sont pas exploitées
                    $fks[] = new ForeignKey((string) $constraintName, $parts[0]['column_name'], $parts[0]['ref_table'], $parts[0]['ref_column']);
                }
            }
            $result[] = new Table($name, $columns[$name] ?? [], $fks, (string) $t['table_comment']);
        }

        return new Schema($database, $result);
    }

    /** @param array<string, mixed> $row ligne de information_schema.COLUMNS */
    public static function buildColumn(array $row, bool $primary, bool $isMariaDb = false): Column
    {
        $dataType = strtolower((string) $row['data_type']);
        $columnType = strtolower((string) $row['column_type']);
        $extra = strtolower((string) ($row['extra'] ?? ''));
        $nullable = strtoupper((string) $row['is_nullable']) === 'YES';
        $kind = self::mapType($dataType, $columnType);
        [$default, $isExpression] = self::normalizeDefault($row['column_default'] ?? null, $extra, $kind, $isMariaDb);

        return new Column(
            name: (string) $row['column_name'],
            kind: $kind,
            sqlType: $columnType,
            nullable: $nullable,
            default: $default,
            defaultIsExpression: $isExpression,
            autoIncrement: str_contains($extra, 'auto_increment'),
            generated: str_contains($extra, 'virtual generated') || str_contains($extra, 'stored generated')
                || str_contains($extra, 'persistent') || $extra === 'virtual',
            primary: $primary,
            maxLength: $kind === ColumnKind::String && $row['max_length'] !== null ? (int) $row['max_length'] : null,
            precision: $row['num_precision'] !== null ? (int) $row['num_precision'] : null,
            scale: $row['num_scale'] !== null ? (int) $row['num_scale'] : null,
            unsigned: str_contains($columnType, 'unsigned'),
            enumValues: $kind === ColumnKind::Enum ? self::parseEnum($columnType) : [],
            comment: (string) ($row['column_comment'] ?? ''),
        );
    }

    public static function mapType(string $dataType, string $columnType): ColumnKind
    {
        return match ($dataType) {
            'tinyint' => str_starts_with($columnType, 'tinyint(1)') ? ColumnKind::Bool : ColumnKind::Int,
            'bit' => in_array($columnType, ['bit', 'bit(1)'], true) ? ColumnKind::Bool : ColumnKind::Binary,
            'smallint', 'mediumint', 'int', 'integer', 'bigint' => ColumnKind::Int,
            'float', 'double', 'real', 'double precision' => ColumnKind::Float,
            'decimal', 'numeric', 'dec', 'fixed' => ColumnKind::Decimal,
            'char', 'varchar', 'set', 'uuid', 'inet4', 'inet6' => ColumnKind::String,
            'tinytext', 'text', 'mediumtext', 'longtext' => ColumnKind::Text,
            'enum' => ColumnKind::Enum,
            'json' => ColumnKind::Json,
            'date' => ColumnKind::Date,
            'datetime', 'timestamp' => ColumnKind::DateTime,
            'time' => ColumnKind::Time,
            'year' => ColumnKind::Year,
            default => ColumnKind::Binary, // binary, varbinary, *blob, géométries…
        };
    }

    /** @return list<string> */
    public static function parseEnum(string $columnType): array
    {
        preg_match_all("/'((?:[^']|'')*)'/", $columnType, $matches);

        return array_map(static fn(string $v): string => str_replace("''", "'", $v), $matches[1]);
    }

    /**
     * MySQL et MariaDB n'exposent pas les valeurs par défaut de la même façon :
     * MariaDB cite les littéraux et écrit « NULL » ; MySQL marque les expressions par DEFAULT_GENERATED.
     *
     * @return array{0: string|null, 1: bool} [littéral, est une expression]
     */
    public static function normalizeDefault(mixed $raw, string $extra, ColumnKind $kind, bool $isMariaDb): array
    {
        if ($raw === null) {
            return [null, false];
        }
        $raw = (string) $raw;

        if ($isMariaDb) {
            if (strtoupper($raw) === 'NULL') {
                return [null, false];
            }
            if (str_starts_with($raw, "'") && str_ends_with($raw, "'") && strlen($raw) >= 2) {
                return [str_replace(["''", '\\\\'], ["'", '\\'], substr($raw, 1, -1)), false];
            }
            if (is_numeric($raw)) {
                return [$raw, false];
            }

            return [null, true];
        }

        if (str_contains($extra, 'default_generated')) {
            return [null, true];
        }
        if (in_array($kind, [ColumnKind::DateTime, ColumnKind::Date, ColumnKind::Time], true)
            && preg_match('/^(current_timestamp|now)/i', $raw) === 1) {
            return [null, true];
        }

        return [$raw, false];
    }

    private function scalar(string $sql): mixed
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute();

        return $statement->fetchColumn();
    }

    /** @return list<array<string, mixed>> */
    private function fetch(string $sql, string $database): array
    {
        // PDO en mode natif refuse un marqueur nommé répété : chaque :db est numéroté.
        $i = 0;
        $sql = (string) preg_replace_callback('/:db\\b/', static function () use (&$i): string {
            return ':db' . $i++;
        }, $sql);
        $statement = $this->pdo->prepare($sql);
        for ($j = 0; $j < $i; ++$j) {
            $statement->bindValue(':db' . $j, $database);
        }
        $statement->execute();

        /** @var list<array<string, mixed>> */
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
