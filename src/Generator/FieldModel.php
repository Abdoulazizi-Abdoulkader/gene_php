<?php

declare(strict_types=1);

namespace Gene\Generator;

use Gene\Schema\Column;
use Gene\Schema\ColumnKind;

/** Une colonne vue par les gabarits : noms PHP, libellé, type de champ, relation éventuelle. */
final readonly class FieldModel
{
    public function __construct(
        public Column $column,
        public string $name,
        public string $property,
        public string $label,
        public string $inputId,
        public ?RelationModel $relation = null,
    ) {}

    public function kind(): string
    {
        return $this->column->kind->value;
    }

    public function phpType(): string
    {
        return $this->column->kind->phpType();
    }

    /** Expression PHP qui convertit $row[...] vers le type de la propriété. */
    public function castExpression(string $rowVar = '$row'): string
    {
        $access = $rowVar . '[' . PhpExporter::export($this->name) . ']';
        $cast = match ($this->column->kind) {
            ColumnKind::Int, ColumnKind::Year => '(int) ' . $access,
            ColumnKind::Float => '(float) ' . $access,
            ColumnKind::Bool => '(bool) ' . $access,
            default => '(string) ' . $access,
        };

        return sprintf('isset(%s) ? %s : null', $access, $cast);
    }

    /** Type de contrôle HTML : text, number, decimal, date, datetime, time, year, bool, textarea, select, enum. */
    public function input(): string
    {
        if ($this->relation !== null) {
            return 'select';
        }

        return match ($this->column->kind) {
            ColumnKind::Int => 'number',
            ColumnKind::Float, ColumnKind::Decimal => 'decimal',
            ColumnKind::Bool => 'bool',
            ColumnKind::Text, ColumnKind::Json => 'textarea',
            ColumnKind::Enum => 'enum',
            ColumnKind::Date => 'date',
            ColumnKind::DateTime => 'datetime',
            ColumnKind::Time => 'time',
            ColumnKind::Year => 'year',
            default => 'text',
        };
    }

    public function isWritable(): bool
    {
        return $this->column->isWritable();
    }

    public function isRequired(): bool
    {
        return $this->column->isRequired();
    }

    /** Colonnes longues : tronquées dans les listes. */
    public function isLong(): bool
    {
        return in_array($this->column->kind, [ColumnKind::Text, ColumnKind::Json], true);
    }

    /**
     * Description du champ exportée dans Entity::FIELDS (lue par App\Framework\Form).
     *
     * @return array<string, mixed>
     */
    public function spec(): array
    {
        $c = $this->column;
        $spec = [
            'label' => $this->label,
            'type' => $c->kind === ColumnKind::Binary ? 'string' : $c->kind->value,
            'required' => $c->isRequired(),
            'nullable' => $c->nullable,
        ];
        if ($c->maxLength !== null) {
            $spec['maxLength'] = $c->maxLength;
        }
        $maxBytes = $this->maxBytes();
        if ($maxBytes !== null) {
            $spec['maxBytes'] = $maxBytes;
        }
        [$min, $max] = $this->integerRange() ?? [null, null];
        if ($min !== null) {
            $spec['min'] = $min;
            $spec['max'] = $max;
        }
        if ($c->enumValues !== []) {
            $spec['choices'] = $c->enumValues;
        }
        if ($c->default !== null) {
            $spec['default'] = $c->default;
        }
        if ($c->kind === ColumnKind::Decimal) {
            $spec['precision'] = $c->precision;
            $spec['scale'] = $c->scale;
        }
        if ($c->unsigned && in_array($c->kind, [ColumnKind::Decimal, ColumnKind::Float], true)) {
            $spec['unsigned'] = true;
        }

        return $spec;
    }

    /**
     * Bornes d'une colonne entière d'après son type SQL (au-delà, MySQL refuse la valeur).
     * BIGINT UNSIGNED est plafonné à PHP_INT_MAX, limite des entiers PHP.
     *
     * @return array{0: int, 1: int}|null
     */
    public function integerRange(): ?array
    {
        if ($this->column->kind !== ColumnKind::Int
            || preg_match('/^(tinyint|smallint|mediumint|int|integer|bigint)\b/', $this->column->sqlType, $m) !== 1) {
            return null;
        }
        $bits = ['tinyint' => 8, 'smallint' => 16, 'mediumint' => 24, 'int' => 32, 'integer' => 32, 'bigint' => 64][$m[1]];
        if ($bits === 64) {
            return $this->column->unsigned ? [0, PHP_INT_MAX] : [PHP_INT_MIN, PHP_INT_MAX];
        }

        return $this->column->unsigned ? [0, 2 ** $bits - 1] : [-(2 ** ($bits - 1)), 2 ** ($bits - 1) - 1];
    }

    /** Taille maximale en octets des colonnes TEXT (LONGTEXT et JSON : pas de limite utile). */
    private function maxBytes(): ?int
    {
        return match (true) {
            str_starts_with($this->column->sqlType, 'tinytext') => 255,
            str_starts_with($this->column->sqlType, 'mediumtext') => 16_777_215,
            str_starts_with($this->column->sqlType, 'text') => 65_535,
            default => null,
        };
    }
}
