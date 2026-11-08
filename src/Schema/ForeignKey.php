<?php

declare(strict_types=1);

namespace Gene\Schema;

/** Clé étrangère sur une seule colonne (les clés composites ne sont pas exploitées pour les listes). */
final readonly class ForeignKey
{
    public function __construct(
        public string $name,
        public string $column,
        public string $referencedTable,
        public string $referencedColumn,
    ) {}
}
