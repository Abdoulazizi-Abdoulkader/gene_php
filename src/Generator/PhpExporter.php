<?php

declare(strict_types=1);

namespace Gene\Generator;

/**
 * Écrit une valeur sous forme de littéral PHP sûr (chaînes entre apostrophes, tableaux courts).
 * Tout nom issu de la base passe par ici avant d'atterrir dans du code généré.
 */
final class PhpExporter
{
    public static function export(mixed $value, int $indent = 0, string $unit = '    '): string
    {
        return match (true) {
            $value === null => 'null',
            is_bool($value) => $value ? 'true' : 'false',
            is_int($value) => (string) $value,
            is_float($value) => var_export($value, true),
            is_string($value) => "'" . strtr($value, ['\\' => '\\\\', "'" => "\\'"]) . "'",
            is_array($value) => self::exportArray($value, $indent, $unit),
            default => throw new \InvalidArgumentException('Type non exportable : ' . get_debug_type($value)),
        };
    }

    /** @param array<mixed> $value */
    private static function exportArray(array $value, int $indent, string $unit): string
    {
        if ($value === []) {
            return '[]';
        }
        $isList = array_is_list($value);
        $scalarList = $isList && array_filter($value, is_array(...)) === [];
        if ($scalarList) {
            $inline = '[' . implode(', ', array_map(static fn(mixed $v): string => self::export($v), $value)) . ']';
            if (strlen($inline) <= 100) {
                return $inline;
            }
        }

        $pad = str_repeat($unit, $indent + 1);
        $lines = [];
        foreach ($value as $key => $item) {
            $prefix = $isList ? '' : self::export($key) . ' => ';
            $lines[] = $pad . $prefix . self::export($item, $indent + 1, $unit) . ',';
        }

        return "[\n" . implode("\n", $lines) . "\n" . str_repeat($unit, $indent) . ']';
    }
}
