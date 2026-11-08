<?php

declare(strict_types=1);

namespace Gene\Schema;

/**
 * Catégorie logique d'une colonne, indépendante du SGBD.
 * Elle pilote le type PHP de la propriété, le champ de formulaire et la validation.
 */
enum ColumnKind: string
{
    case Int = 'int';
    case Float = 'float';
    case Decimal = 'decimal';
    case Bool = 'bool';
    case String = 'string';
    case Text = 'text';
    case Date = 'date';
    case DateTime = 'datetime';
    case Time = 'time';
    case Year = 'year';
    case Enum = 'enum';
    case Json = 'json';
    case Binary = 'binary';

    /** Type PHP (non nullable) de la propriété d'entité générée. */
    public function phpType(): string
    {
        return match ($this) {
            self::Int, self::Year => 'int',
            self::Float => 'float',
            self::Bool => 'bool',
            default => 'string',
        };
    }

    /** Les colonnes binaires ne sont ni listées ni éditables dans l'interface générée. */
    public function isEditable(): bool
    {
        return $this !== self::Binary;
    }

    public function isTextual(): bool
    {
        return $this === self::String || $this === self::Text || $this === self::Enum;
    }
}
