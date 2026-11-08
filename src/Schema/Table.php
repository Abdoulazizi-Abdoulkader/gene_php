<?php

declare(strict_types=1);

namespace Gene\Schema;

final readonly class Table
{
    /** @var array<string, Column> */
    public array $columns;

    /** @var array<string, ForeignKey> indexé par nom de colonne */
    public array $foreignKeys;

    /**
     * @param list<Column>     $columns     dans l'ordre de la table
     * @param list<ForeignKey> $foreignKeys
     */
    public function __construct(
        public string $name,
        array $columns,
        array $foreignKeys = [],
        public string $comment = '',
    ) {
        $byName = [];
        foreach ($columns as $column) {
            $byName[$column->name] = $column;
        }
        $this->columns = $byName;

        $fks = [];
        foreach ($foreignKeys as $fk) {
            if (isset($byName[$fk->column])) {
                $fks[$fk->column] = $fk;
            }
        }
        $this->foreignKeys = $fks;
    }

    public function column(string $name): Column
    {
        return $this->columns[$name] ?? throw new \OutOfBoundsException(sprintf('Colonne « %s » absente de la table « %s ».', $name, $this->name));
    }

    /** @return list<Column> */
    public function primaryKey(): array
    {
        return array_values(array_filter($this->columns, static fn(Column $c): bool => $c->primary));
    }

    public function hasPrimaryKey(): bool
    {
        return $this->primaryKey() !== [];
    }

    /** Clé primaire sur une seule colonne : seul cas où le CRUD complet est généré. */
    public function singlePrimaryKey(): ?Column
    {
        $pk = $this->primaryKey();

        return count($pk) === 1 ? $pk[0] : null;
    }

    public function foreignKeyFor(string $column): ?ForeignKey
    {
        return $this->foreignKeys[$column] ?? null;
    }

    /**
     * Colonne servant de libellé lisible (listes déroulantes, colonnes de clé étrangère) :
     * la première colonne textuelle qui n'est ni clé primaire ni clé étrangère, sinon la clé primaire.
     */
    public function labelColumn(): Column
    {
        $preferred = ['libelle', 'label', 'nom', 'name', 'titre', 'title', 'intitule', 'designation', 'code'];
        foreach ($preferred as $wanted) {
            foreach ($this->columns as $column) {
                if (strtolower($column->name) === $wanted && $column->kind->isTextual()) {
                    return $column;
                }
            }
        }
        foreach ($this->columns as $column) {
            if ($column->kind->isTextual() && !$column->primary && !isset($this->foreignKeys[$column->name])) {
                return $column;
            }
        }

        return $this->primaryKey()[0] ?? array_values($this->columns)[0];
    }
}
