<?php

declare(strict_types=1);

namespace Gene\Schema;

final readonly class Schema
{
    /** @var array<string, Table> */
    public array $tables;

    /** @param list<Table> $tables */
    public function __construct(public string $database, array $tables)
    {
        $byName = [];
        foreach ($tables as $table) {
            $byName[$table->name] = $table;
        }
        ksort($byName, SORT_NATURAL | SORT_FLAG_CASE);
        $this->tables = $byName;
    }

    public function table(string $name): ?Table
    {
        return $this->tables[$name] ?? null;
    }

    /**
     * « client, commande » → [client, commande] (saisie d'une liste de tables, CLI ou web).
     *
     * @return list<string>
     */
    public static function parseTableList(?string $value): array
    {
        return array_values(array_filter(array_map(trim(...), explode(',', $value ?? '')), static fn(string $t): bool => $t !== ''));
    }

    /**
     * Restreint le schéma à une sélection de tables.
     *
     * @param list<string> $only    tables à garder (vide = toutes)
     * @param list<string> $exclude tables à retirer
     */
    public function filter(array $only = [], array $exclude = []): self
    {
        $unknown = array_diff([...$only, ...$exclude], array_keys($this->tables));
        if ($unknown !== []) {
            throw new \InvalidArgumentException('Table(s) inconnue(s) : ' . implode(', ', $unknown));
        }
        $tables = array_filter(
            $this->tables,
            static fn(Table $t): bool => ($only === [] || in_array($t->name, $only, true)) && !in_array($t->name, $exclude, true),
        );

        return new self($this->database, array_values($tables));
    }
}
