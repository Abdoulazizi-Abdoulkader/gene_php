<?php

declare(strict_types=1);

namespace App\Framework;

use PDO;

/**
 * Accès aux données d'une table, piloté par les constantes déclarées dans la classe fille générée.
 * Tous les noms SQL viennent de ces constantes ; les valeurs passent par des requêtes préparées.
 *
 * @template T of Entity
 */
abstract class Repository
{
    /** Nom de la table. */
    protected const TABLE = '';

    /** Colonne de clé primaire (null pour une clé composite : lecture seule). */
    protected const PRIMARY_KEY = null;

    /** La clé primaire est-elle attribuée par AUTO_INCREMENT ? */
    protected const AUTO_INCREMENT = false;

    /** Colonnes lues (les binaires sont exclues). @var list<string> */
    protected const COLUMNS = [];

    /** Colonnes écrites à l'insertion et à la mise à jour. @var list<string> */
    protected const WRITABLE = [];

    /** Colonnes NOT NULL à valeur par défaut calculée (CURRENT_TIMESTAMP…) : non écrites si la saisie est vide. @var list<string> */
    protected const KEEP_IF_NULL = [];

    /** Clés étrangères : colonne => [table liée, colonne liée, colonne libellé]. @var array<string, array{0: string, 1: string, 2: string}> */
    protected const RELATIONS = [];

    /** Colonne libellé utilisée par options(). */
    protected const LABEL_COLUMN = null;

    /** Colonnes textuelles explorées par la recherche de la liste. @var list<string> */
    protected const SEARCHABLE = [];

    /** @return class-string<T> */
    abstract protected function entityClass(): string;

    public function __construct(protected readonly PDO $pdo) {}

    /** @return T|null */
    public function find(string|int $id): ?Entity
    {
        $sql = sprintf('SELECT %s FROM %s WHERE %s = :id', $this->selectList(), $this->table(), $this->pk());
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['id' => $id]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return is_array($row) ? $this->hydrate($row) : null;
    }

    public function count(string $search = ''): int
    {
        [$where, $params] = $this->searchClause($search, '');

        return (int) $this->run('SELECT COUNT(*) FROM ' . $this->table() . $where, $params)->fetchColumn();
    }

    /**
     * Page de résultats triée, filtrée par une recherche plein texte simple (LIKE sur SEARCHABLE).
     *
     * @return Page<T>
     */
    public function paginate(int $page = 1, int $perPage = 20, ?string $sort = null, string $direction = 'asc', string $search = ''): Page
    {
        $sort = in_array($sort, static::COLUMNS, true) ? $sort : (static::PRIMARY_KEY ?? static::COLUMNS[0]);
        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';
        $search = trim($search);
        $total = $this->count($search);
        $page = min(max(1, $page), max(1, (int) ceil($total / max(1, $perPage))));

        $select = [];
        foreach (static::COLUMNS as $column) {
            $select[] = 't.' . Database::quote($column);
        }
        $joins = [];
        $i = 0;
        foreach (static::RELATIONS as $column => [$refTable, $refColumn, $labelColumn]) {
            $alias = 'r' . $i;
            $select[] = sprintf('%s.%s AS %s', $alias, Database::quote($labelColumn), Database::quote('__label_' . $i));
            $joins[] = sprintf(
                'LEFT JOIN %s AS %s ON %s.%s = t.%s',
                Database::quote($refTable),
                $alias,
                $alias,
                Database::quote($refColumn),
                Database::quote($column),
            );
            ++$i;
        }

        $order = 't.' . Database::quote($sort) . ' ' . strtoupper($direction);
        if (static::PRIMARY_KEY !== null && $sort !== static::PRIMARY_KEY) {
            $order .= ', t.' . Database::quote(static::PRIMARY_KEY); // ordre stable entre les pages
        }
        [$where, $params] = $this->searchClause($search, 't.');
        $sql = sprintf(
            'SELECT %s FROM %s AS t %s%s ORDER BY %s LIMIT :limit OFFSET :offset',
            implode(', ', $select),
            $this->table(),
            implode(' ', $joins),
            $where,
            $order,
        );
        $statement = $this->pdo->prepare($sql);
        foreach ($params as $name => $value) {
            $statement->bindValue($name, $value);
        }
        $statement->bindValue('limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue('offset', ($page - 1) * $perPage, PDO::PARAM_INT);
        $statement->execute();

        $items = [];
        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $labels = [];
            $i = 0;
            foreach (array_keys(static::RELATIONS) as $column) {
                $label = $row['__label_' . $i] ?? null;
                $labels[(string) $column] = $label === null ? null : (string) $label;
                unset($row['__label_' . $i]);
                ++$i;
            }
            $items[] = ['entity' => $this->hydrate($row), 'labels' => $labels];
        }

        return new Page($items, $total, $page, $perPage, $sort, $direction, $search);
    }

    /**
     * Insère l'entité. Les valeurs nulles ne sont pas écrites : la base applique ses valeurs par défaut.
     *
     * @param T $entity
     *
     * @return string|null identifiant de la ligne créée ; null s'il a été produit par la base sans être récupérable
     *                     (clé non auto-incrémentée remplie par un DEFAULT, UUID() par exemple)
     */
    public function insert(Entity $entity): ?string
    {
        $row = array_filter($this->writableRow($entity), static fn(mixed $v): bool => $v !== null);
        if ($row === []) {
            $sql = sprintf('INSERT INTO %s () VALUES ()', $this->table());
        } else {
            $sql = sprintf(
                'INSERT INTO %s (%s) VALUES (%s)',
                $this->table(),
                implode(', ', array_map(Database::quote(...), array_keys($row))),
                implode(', ', array_map(static fn(int $i): string => ':v' . $i, array_keys(array_values($row)))),
            );
        }
        $statement = $this->pdo->prepare($sql);
        $statement->execute($this->bindings($row));

        $pk = static::PRIMARY_KEY;
        if ($pk !== null && isset($row[$pk])) {
            return (string) $row[$pk];
        }

        return static::AUTO_INCREMENT ? (string) $this->pdo->lastInsertId() : null;
    }

    /**
     * @param T $entity
     */
    public function update(string|int $id, Entity $entity): void
    {
        $row = $this->writableRow($entity);
        foreach (static::KEEP_IF_NULL as $column) {
            if (array_key_exists($column, $row) && $row[$column] === null) {
                unset($row[$column]);
            }
        }
        if ($row === []) {
            return;
        }
        $assignments = [];
        $i = 0;
        foreach (array_keys($row) as $column) {
            $assignments[] = Database::quote($column) . ' = :v' . $i++;
        }
        $sql = sprintf('UPDATE %s SET %s WHERE %s = :id', $this->table(), implode(', ', $assignments), $this->pk());
        $statement = $this->pdo->prepare($sql);
        $statement->execute([...$this->bindings($row), 'id' => $id]);
    }

    public function delete(string|int $id): void
    {
        $statement = $this->pdo->prepare(sprintf('DELETE FROM %s WHERE %s = :id', $this->table(), $this->pk()));
        $statement->execute(['id' => $id]);
    }

    /**
     * Couples identifiant => libellé pour les listes déroulantes des tables qui référencent celle-ci.
     * La valeur actuelle ($current) figure toujours dans la liste, même au-delà de la limite :
     * sans cela, enregistrer le formulaire effacerait la relation.
     *
     * @return array<string, string>
     */
    public function options(?string $current = null, int $limit = 1000): array
    {
        $pk = static::PRIMARY_KEY ?? throw new \LogicException(static::class . ' : pas de clé primaire simple.');
        $label = static::LABEL_COLUMN ?? $pk;
        $sql = sprintf(
            'SELECT %s AS id, %s AS label FROM %s ORDER BY %s LIMIT %d',
            Database::quote($pk),
            Database::quote($label),
            $this->table(),
            Database::quote($label),
            $limit,
        );
        $options = [];
        foreach ($this->run($sql)->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $options[(string) $row['id']] = (string) ($row['label'] ?? $row['id']);
        }
        if ($current !== null && $current !== '' && !isset($options[$current])) {
            $label = $this->labelFor($current);
            if ($label !== null) {
                $options = [$current => $label] + $options;
            }
        }

        return $options;
    }

    /** Libellé d'un enregistrement (affichage d'une clé étrangère sur une fiche). */
    public function labelFor(string|int $id): ?string
    {
        $label = static::LABEL_COLUMN ?? static::PRIMARY_KEY;
        if ($label === null) {
            return null;
        }
        $statement = $this->pdo->prepare(sprintf('SELECT %s FROM %s WHERE %s = :id', Database::quote($label), $this->table(), $this->pk()));
        $statement->execute(['id' => $id]);
        $value = $statement->fetchColumn();

        return $value === false || $value === null ? null : (string) $value;
    }

    /**
     * @param array<string, mixed> $row
     *
     * @return T
     */
    protected function hydrate(array $row): Entity
    {
        $class = $this->entityClass();

        return $class::fromRow($row);
    }

    /**
     * @param T $entity
     *
     * @return array<string, mixed>
     */
    protected function writableRow(Entity $entity): array
    {
        return array_intersect_key($entity->toRow(), array_flip(static::WRITABLE));
    }

    /**
     * @param array<string, mixed> $row
     *
     * @return array<string, mixed>
     */
    private function bindings(array $row): array
    {
        $bindings = [];
        $i = 0;
        foreach ($row as $value) {
            $bindings['v' . $i++] = is_bool($value) ? (int) $value : $value;
        }

        return $bindings;
    }

    /**
     * Requête personnalisée qui renvoie des entités. Les valeurs passent toujours en paramètres (:nom),
     * jamais par concaténation.
     *
     * Exemple : $this->findBySql('SELECT * FROM `client` WHERE pays_code = :code', ['code' => 'NE'])
     *
     * @param array<string, mixed> $params
     *
     * @return list<T>
     */
    protected function findBySql(string $sql, array $params = []): array
    {
        $entities = [];
        foreach ($this->run($sql, $params)->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $entities[] = $this->hydrate($row);
        }

        return $entities;
    }

    /**
     * Exécute une requête préparée et renvoie le résultat (PDO lève une exception en cas d'erreur).
     *
     * @param array<string, mixed> $params
     */
    protected function run(string $sql, array $params = []): \PDOStatement
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);

        return $statement;
    }

    /**
     * @return array{0: string, 1: array<string, string>} clause WHERE (vide sans recherche) et paramètres
     */
    private function searchClause(string $search, string $prefix): array
    {
        if ($search === '' || static::SEARCHABLE === []) {
            return ['', []];
        }
        $pattern = '%' . addcslashes($search, '\\%_') . '%';
        $conditions = [];
        $params = [];
        foreach (static::SEARCHABLE as $i => $column) {
            $conditions[] = $prefix . Database::quote($column) . ' LIKE :q' . $i;
            $params['q' . $i] = $pattern;
        }

        return [' WHERE (' . implode(' OR ', $conditions) . ')', $params];
    }

    private function table(): string
    {
        return Database::quote(static::TABLE);
    }

    private function pk(): string
    {
        return Database::quote(static::PRIMARY_KEY ?? throw new \LogicException(static::class . ' : pas de clé primaire simple.'));
    }

    private function selectList(): string
    {
        return implode(', ', array_map(Database::quote(...), static::COLUMNS));
    }
}
