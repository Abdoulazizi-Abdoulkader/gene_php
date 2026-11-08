<?php

declare(strict_types=1);

namespace App\Framework;

/**
 * Conversion et validation des formulaires, pilotées par la description des champs
 * (constante FIELDS des entités générées). Les clés sont les noms de colonnes.
 *
 * Clés d'un champ : maxLength (caractères), maxBytes (octets, colonnes TEXT), min/max (bornes des entiers),
 * unsigned (décimaux), precision/scale (DECIMAL), choices (ENUM), default (valeur littérale du schéma).
 *
 * @phpstan-type Field array{
 *     label: string, type: string, required: bool, nullable: bool,
 *     maxLength?: int, maxBytes?: int, min?: int, max?: int, choices?: list<string>,
 *     default?: string, precision?: int|null, scale?: int|null, unsigned?: bool
 * }
 */
final class Form
{
    /**
     * Valeurs initiales d'un formulaire de création (valeurs par défaut littérales du schéma).
     *
     * @param array<string, Field> $fields
     *
     * @return array<string, string>
     */
    public static function defaults(array $fields): array
    {
        $values = [];
        foreach ($fields as $name => $field) {
            $values[$name] = self::toInput($field['type'], $field['default'] ?? null);
        }

        return $values;
    }

    /**
     * Valeurs d'un formulaire d'édition, à partir d'une ligne existante.
     *
     * @param array<string, mixed> $row
     * @param array<string, Field> $fields
     *
     * @return array<string, string>
     */
    public static function fromRow(array $row, array $fields): array
    {
        $values = [];
        foreach ($fields as $name => $field) {
            $values[$name] = self::toInput($field['type'], $row[$name] ?? null);
        }

        return $values;
    }

    /**
     * @param array<string, string> $input saisie brute ($_POST)
     * @param array<string, Field>  $fields
     */
    public static function validate(array $input, array $fields): FormResult
    {
        $values = [];
        $errors = [];
        $raw = [];

        foreach ($fields as $name => $field) {
            $type = $field['type'];
            $value = trim($input[$name] ?? '');
            $raw[$name] = $value;

            if ($type === 'bool') {
                $values[$name] = in_array(strtolower($value), ['1', 'on', 'true', 'oui'], true);
                continue;
            }
            if ($value === '') {
                if ($field['required']) {
                    $errors[$name] = 'Ce champ est obligatoire.';
                    $values[$name] = null;
                    continue;
                }
                if ($field['nullable'] || !isset($field['default'])) {
                    $values[$name] = null; // NULL, ou valeur par défaut de la base à l'insertion
                    continue;
                }
                // Colonne NOT NULL vidée : on revient à sa valeur par défaut (une chaîne vide reste vide).
                $value = $field['default'];
                if ($value === '') {
                    $values[$name] = '';
                    continue;
                }
            }

            [$converted, $error] = self::convert($value, $field);
            if ($error !== null) {
                $errors[$name] = $error;
            } else {
                $values[$name] = $converted;
            }
        }

        return new FormResult($values, $errors, $raw);
    }

    /**
     * @param Field $field
     *
     * @return array{0: mixed, 1: string|null}
     */
    private static function convert(string $value, array $field): array
    {
        switch ($field['type']) {
            case 'int':
            case 'year':
                $int = filter_var($value, FILTER_VALIDATE_INT);
                if ($int === false) {
                    return [null, 'Nombre entier attendu.'];
                }
                if ($field['type'] === 'year' && ($int < 1901 || $int > 2155)) {
                    return [null, 'Année entre 1901 et 2155 attendue.'];
                }
                $min = $field['min'] ?? null;
                $max = $field['max'] ?? null;
                if (($min !== null && $int < $min) || ($max !== null && $int > $max)) {
                    return [null, sprintf('Nombre entre %s et %s attendu.', $min ?? '−∞', $max ?? '+∞')];
                }

                return [$int, null];

            case 'float':
                $normalized = str_replace([' ', ','], ['', '.'], $value);
                if (!is_numeric($normalized) || !is_finite((float) $normalized)) {
                    return [null, 'Nombre attendu.'];
                }
                if (($field['unsigned'] ?? false) && (float) $normalized < 0) {
                    return [null, 'Le nombre doit être positif.'];
                }

                return [(float) $normalized, null];

            case 'decimal':
                $normalized = str_replace([' ', ','], ['', '.'], $value);
                if (preg_match('/^([+-]?)(\d*)(?:\.(\d*))?$/', $normalized, $m) !== 1 || ($m[2] === '' && ($m[3] ?? '') === '')) {
                    return [null, 'Nombre décimal attendu.'];
                }
                $integer = ltrim($m[2], '0');
                $fraction = rtrim($m[3] ?? '', '0');
                $scale = $field['scale'] ?? null;
                $precision = $field['precision'] ?? null;
                if ($scale !== null && $precision !== null
                    && (strlen($integer) > $precision - $scale || strlen($fraction) > $scale)) {
                    return [null, sprintf('Au plus %d chiffre(s) avant la virgule et %d après.', $precision - $scale, $scale)];
                }
                if (($field['unsigned'] ?? false) && $m[1] === '-') {
                    return [null, 'Le nombre doit être positif.'];
                }

                return [($m[1] === '-' ? '-' : '') . ($integer === '' ? '0' : $integer) . ($fraction !== '' ? '.' . $fraction : ''), null];

            case 'enum':
                return in_array($value, $field['choices'] ?? [], true) ? [$value, null] : [null, 'Valeur non autorisée.'];

            case 'date':
                return self::parseDate($value, ['Y-m-d'], 'Y-m-d', 'Date attendue (AAAA-MM-JJ).');

            case 'datetime':
                return self::parseDate($value, ['Y-m-d\TH:i', 'Y-m-d\TH:i:s', 'Y-m-d H:i:s', 'Y-m-d H:i'], 'Y-m-d H:i:s', 'Date et heure attendues.');

            case 'time':
                return self::parseDate($value, ['H:i', 'H:i:s'], 'H:i:s', 'Heure attendue (HH:MM).');

            case 'json':
                $maxBytes = $field['maxBytes'] ?? null;
                if ($maxBytes !== null && strlen($value) > $maxBytes) {
                    return [null, 'Texte trop long.'];
                }

                return json_validate($value) ? [$value, null] : [null, 'JSON invalide.'];

            default: // string, text
                $maxLength = $field['maxLength'] ?? null;
                if ($maxLength !== null && mb_strlen($value) > $maxLength) {
                    return [null, sprintf('Au plus %d caractère(s).', $maxLength)];
                }
                $maxBytes = $field['maxBytes'] ?? null;
                if ($maxBytes !== null && strlen($value) > $maxBytes) {
                    return [null, sprintf('Texte trop long : %s octets au plus.', number_format($maxBytes, 0, ',', ' '))];
                }

                return [$value, null];
        }
    }

    /**
     * @param list<string> $formats
     *
     * @return array{0: string|null, 1: string|null}
     */
    private static function parseDate(string $value, array $formats, string $output, string $error): array
    {
        foreach ($formats as $format) {
            $date = \DateTimeImmutable::createFromFormat('!' . $format, $value);
            $warnings = \DateTimeImmutable::getLastErrors();
            if ($date !== false && ($warnings === false || ($warnings['warning_count'] === 0 && $warnings['error_count'] === 0))) {
                return [$date->format($output), null];
            }
        }

        return [null, $error];
    }

    /** Valeur stockée → valeur d'un champ HTML. */
    private static function toInput(string $type, mixed $value): string
    {
        if ($value === null) {
            return '';
        }
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }
        $value = (string) $value;

        return match ($type) {
            'bool' => in_array(strtolower($value), ['1', "b'1'", 'true'], true) ? '1' : '0',
            // Secondes conservées : une modification sans changement ne doit pas réécrire la valeur.
            'datetime' => str_replace(' ', 'T', substr($value, 0, 19)),
            'date' => substr($value, 0, 10),
            default => $value,
        };
    }
}
