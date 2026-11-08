<?php

declare(strict_types=1);

namespace Gene\Generator;

use Gene\Schema\ColumnKind;
use Gene\Schema\Table;

/** Une table vue par les gabarits. */
final readonly class EntityModel
{
    /** Nombre maximal de colonnes affichées dans une liste. */
    private const LIST_COLUMNS = 8;

    /** @param list<FieldModel> $fields */
    public function __construct(
        public Table $table,
        public string $name,
        public string $className,
        public string $slug,
        public string $title,
        public array $fields,
        public ?FieldModel $primaryKey,
        public FieldModel $labelField,
    ) {}

    /** Clé primaire composite : liste seule, sans fiche ni édition. */
    public function readOnly(): bool
    {
        return $this->primaryKey === null;
    }

    public function path(): string
    {
        return '/' . $this->slug;
    }

    /** @return list<FieldModel> colonnes lues (binaires exclues) */
    public function readableFields(): array
    {
        return array_values(array_filter($this->fields, static fn(FieldModel $f): bool => $f->column->kind !== ColumnKind::Binary));
    }

    /** @return list<FieldModel> */
    public function formFields(): array
    {
        return array_values(array_filter($this->fields, static fn(FieldModel $f): bool => $f->isWritable()));
    }

    /** @return list<FieldModel> colonnes de la liste : clé primaire puis colonnes courtes */
    public function listFields(): array
    {
        $fields = array_values(array_filter($this->readableFields(), static fn(FieldModel $f): bool => !$f->isLong()));
        if ($fields === []) {
            $fields = $this->readableFields();
        }

        return array_slice($fields, 0, self::LIST_COLUMNS);
    }

    /** @return list<RelationModel> */
    public function relations(): array
    {
        $relations = [];
        foreach ($this->fields as $field) {
            if ($field->relation !== null) {
                $relations[] = $field->relation;
            }
        }

        return $relations;
    }

    /**
     * Relations dédoublonnées par table cible (un dépôt par table dans le contrôleur).
     *
     * @return list<RelationModel>
     */
    public function relatedRepositories(): array
    {
        $unique = [];
        foreach ($this->relations() as $relation) {
            $unique[$relation->className] ??= $relation;
        }

        return array_values($unique);
    }

    /** @return list<string> */
    public function columnNames(): array
    {
        return array_map(static fn(FieldModel $f): string => $f->name, $this->readableFields());
    }

    /** @return list<string> */
    public function writableColumns(): array
    {
        return array_map(static fn(FieldModel $f): string => $f->name, $this->formFields());
    }

    /**
     * Colonnes NOT NULL à défaut calculé (CURRENT_TIMESTAMP…) : vides, elles ne sont pas écrites
     * (la base applique son défaut à l'insertion, la valeur existante est gardée à la modification).
     * Les défauts littéraux sont, eux, appliqués par App\Framework\Form.
     *
     * @return list<string>
     */
    public function keepIfNullColumns(): array
    {
        $columns = [];
        foreach ($this->formFields() as $field) {
            $c = $field->column;
            if (!$c->nullable && $c->defaultIsExpression) {
                $columns[] = $c->name;
            }
        }

        return $columns;
    }

    /** @return list<string> colonnes textuelles courtes, explorées par la recherche de la liste */
    public function searchableColumns(): array
    {
        $columns = [];
        foreach ($this->readableFields() as $field) {
            if ($field->column->kind === ColumnKind::String || $field->column->kind === ColumnKind::Enum) {
                $columns[] = $field->name;
            }
        }

        return $columns;
    }

    public function autoIncrement(): bool
    {
        return $this->primaryKey !== null && $this->primaryKey->column->autoIncrement;
    }

    /** @return array<string, array<string, mixed>> */
    public function fieldSpecs(): array
    {
        $specs = [];
        foreach ($this->formFields() as $field) {
            $specs[$field->name] = $field->spec();
        }

        return $specs;
    }

    /** @return array<string, list<string>> */
    public function relationConstant(): array
    {
        $relations = [];
        foreach ($this->relations() as $r) {
            $relations[$r->column] = [$r->table, $r->referencedColumn, $r->labelColumn];
        }

        return $relations;
    }

    public function hasField(string $kind): bool
    {
        foreach ($this->formFields() as $field) {
            if ($field->kind() === $kind) {
                return true;
            }
        }

        return false;
    }
}
