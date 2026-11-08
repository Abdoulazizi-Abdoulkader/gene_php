<?php

declare(strict_types=1);

namespace Gene\Schema;

final readonly class Column
{
    /**
     * @param list<string> $enumValues valeurs autorisées pour un ENUM/SET
     * @param string|null  $default    valeur littérale par défaut (null si aucune ou si expression)
     */
    public function __construct(
        public string $name,
        public ColumnKind $kind,
        public string $sqlType,
        public bool $nullable = false,
        public ?string $default = null,
        public bool $defaultIsExpression = false,
        public bool $autoIncrement = false,
        public bool $generated = false,
        public bool $primary = false,
        public ?int $maxLength = null,
        public ?int $precision = null,
        public ?int $scale = null,
        public bool $unsigned = false,
        public array $enumValues = [],
        public string $comment = '',
    ) {}

    /** La base fournit-elle une valeur si on n'en donne pas à l'insertion ? */
    public function hasDatabaseDefault(): bool
    {
        return $this->autoIncrement || $this->generated || $this->default !== null || $this->defaultIsExpression || $this->nullable;
    }

    /** Colonne saisissable dans un formulaire (ni auto-incrément, ni calculée, ni binaire). */
    public function isWritable(): bool
    {
        return !$this->autoIncrement && !$this->generated && $this->kind->isEditable();
    }

    /** Un formulaire doit-il exiger une saisie ? (les booléens valent false quand la case est vide) */
    public function isRequired(): bool
    {
        return $this->isWritable() && !$this->hasDatabaseDefault() && $this->kind !== ColumnKind::Bool;
    }
}
