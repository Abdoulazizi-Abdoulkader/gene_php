<?php

declare(strict_types=1);

namespace Gene\Generator;

use Gene\Naming\Inflector;
use Gene\Schema\Schema;
use Gene\Schema\Table;

/**
 * Traduit le schéma en modèles prêts pour les gabarits, et signale ce qui ne peut pas être généré.
 */
final class ModelBuilder
{
    /** Noms déjà pris par le squelette. */
    private const RESERVED_CLASSES = ['Home'];
    private const RESERVED_SLUGS = ['assets'];

    /** @var list<string> */
    private array $warnings = [];

    /** @return list<EntityModel> */
    public function build(Schema $schema): array
    {
        $this->warnings = [];

        /** @var array<string, Table> $tables */
        $tables = [];
        foreach ($schema->tables as $table) {
            if (!$table->hasPrimaryKey()) {
                $this->warnings[] = sprintf('Table « %s » ignorée : elle n\'a pas de clé primaire.', $table->name);
                continue;
            }
            if ($table->singlePrimaryKey() === null) {
                $this->warnings[] = sprintf('Table « %s » : clé primaire composite, seule la liste est générée.', $table->name);
            }
            $tables[$table->name] = $table;
        }

        $classes = [];
        $slugs = [];
        foreach ($tables as $table) {
            $class = Inflector::className($table->name);
            if (in_array($class, self::RESERVED_CLASSES, true)) {
                $class .= 'Table';
            }
            $slug = Inflector::slug($table->name);
            if (in_array($slug, self::RESERVED_SLUGS, true)) {
                $slug .= '-table';
            }
            if (isset($classes[$class])) {
                throw new \RuntimeException(sprintf('Les tables « %s » et « %s » donnent la même classe %s : renommez-en une ou excluez-la (--exclude).', $classes[$class], $table->name, $class));
            }
            if (isset($slugs[$slug])) {
                throw new \RuntimeException(sprintf('Les tables « %s » et « %s » donnent la même URL /%s.', $slugs[$slug], $table->name, $slug));
            }
            $classes[$class] = $table->name;
            $slugs[$slug] = $table->name;
        }
        $classByTable = array_flip($classes);
        $slugByTable = array_flip($slugs);

        $models = [];
        foreach ($tables as $table) {
            $fields = [];
            $properties = [];
            foreach ($table->columns as $column) {
                $property = Inflector::propertyName($column->name);
                $base = $property;
                for ($n = 2; isset($properties[$property]); ++$n) {
                    $property = $base . $n;
                }
                $properties[$property] = true;

                $relation = null;
                $fk = $table->foreignKeyFor($column->name);
                if ($fk !== null) {
                    $target = $tables[$fk->referencedTable] ?? null;
                    $targetPk = $target?->singlePrimaryKey();
                    if ($target === null || $targetPk === null) {
                        $this->warnings[] = sprintf('%s.%s → %s : table cible non générée, champ saisi librement.', $table->name, $column->name, $fk->referencedTable);
                    } elseif ($targetPk->name !== $fk->referencedColumn) {
                        $this->warnings[] = sprintf('%s.%s → %s.%s : la cible n\'est pas la clé primaire, champ saisi librement.', $table->name, $column->name, $fk->referencedTable, $fk->referencedColumn);
                    } else {
                        $targetClass = $classByTable[$target->name];
                        $relation = new RelationModel(
                            column: $column->name,
                            table: $target->name,
                            referencedColumn: $fk->referencedColumn,
                            labelColumn: $target->labelColumn()->name,
                            className: $targetClass,
                            slug: $slugByTable[$target->name],
                            repositoryProperty: lcfirst($targetClass) . 'Repository',
                        );
                    }
                }

                $fields[] = new FieldModel(
                    column: $column,
                    name: $column->name,
                    property: $property,
                    label: self::fieldLabel($column->name, $relation),
                    inputId: 'f-' . Inflector::slug($column->name),
                    relation: $relation,
                );
            }

            $pkName = $table->singlePrimaryKey()?->name;
            $labelName = $table->labelColumn()->name;
            $byName = [];
            foreach ($fields as $field) {
                $byName[$field->name] = $field;
            }

            $models[] = new EntityModel(
                table: $table,
                name: $table->name,
                className: $classByTable[$table->name],
                slug: $slugByTable[$table->name],
                title: Inflector::title($table->name),
                fields: $fields,
                primaryKey: $pkName !== null ? $byName[$pkName] : null,
                labelField: $byName[$labelName],
            );
        }

        return $models;
    }

    /** « pays_code » → « Pays » quand la colonne référence pays.code ; sinon libellé usuel. */
    private static function fieldLabel(string $column, ?RelationModel $relation): string
    {
        if ($relation !== null) {
            $suffix = '_' . strtolower($relation->referencedColumn);
            if (strlen($column) > strlen($suffix) && str_ends_with(strtolower($column), $suffix)) {
                return Inflector::label(substr($column, 0, -strlen($suffix)));
            }
        }

        return Inflector::label($column);
    }

    /** @return list<string> */
    public function warnings(): array
    {
        return $this->warnings;
    }
}
