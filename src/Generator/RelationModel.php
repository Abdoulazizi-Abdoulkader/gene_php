<?php

declare(strict_types=1);

namespace Gene\Generator;

/** Clé étrangère exploitable : la table cible est générée et la colonne cible est sa clé primaire. */
final readonly class RelationModel
{
    public function __construct(
        public string $column,
        public string $table,
        public string $referencedColumn,
        public string $labelColumn,
        public string $className,
        public string $slug,
        public string $repositoryProperty,
    ) {}
}
