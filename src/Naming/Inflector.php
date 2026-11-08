<?php

declare(strict_types=1);

namespace Gene\Naming;

/**
 * Transforme des noms SQL (snake_case, accents, chiffres…) en identifiants PHP valides et en libellés lisibles.
 * Aucun passage au singulier : il casse trop de mots français (« pays », « fils », « prix »).
 */
final class Inflector
{
    /** Mots réservés de PHP interdits comme nom de classe. */
    private const RESERVED = [
        'abstract', 'and', 'array', 'as', 'bool', 'break', 'callable', 'case', 'catch', 'class', 'clone', 'const',
        'continue', 'declare', 'default', 'do', 'echo', 'else', 'elseif', 'empty', 'enddeclare', 'endfor',
        'endforeach', 'endif', 'endswitch', 'endwhile', 'enum', 'eval', 'exit', 'extends', 'false', 'final',
        'finally', 'float', 'fn', 'for', 'foreach', 'function', 'global', 'goto', 'if', 'implements', 'include',
        'instanceof', 'insteadof', 'int', 'interface', 'isset', 'iterable', 'list', 'match', 'mixed', 'namespace',
        'never', 'new', 'null', 'object', 'or', 'parent', 'print', 'private', 'protected', 'public', 'readonly',
        'require', 'resource', 'return', 'self', 'static', 'string', 'switch', 'throw', 'trait', 'true', 'try',
        'unset', 'use', 'var', 'void', 'while', 'xor', 'yield',
    ];

    /**
     * Découpe un nom en mots ASCII : « date_Naissance », « dateNaissance », « Date-naissance » → [date, naissance].
     *
     * @return list<string>
     */
    public static function words(string $name): array
    {
        $ascii = self::ascii($name);
        $ascii = (string) preg_replace('/([a-z0-9])([A-Z])/', '$1 $2', $ascii);
        $ascii = (string) preg_replace('/([A-Z]+)([A-Z][a-z])/', '$1 $2', $ascii);
        $parts = preg_split('/[^A-Za-z0-9]+/', $ascii, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return array_map(strtolower(...), $parts);
    }

    /** « gestion_courrier » → « GestionCourrier » (classe). */
    public static function className(string $name): string
    {
        $class = implode('', array_map(ucfirst(...), self::words($name)));
        if ($class === '') {
            $class = 'Table' . substr(md5($name), 0, 6);
        }
        if (ctype_digit($class[0])) {
            $class = 'T' . $class;
        }
        if (in_array(strtolower($class), self::RESERVED, true)) {
            $class .= 'Item';
        }

        return $class;
    }

    /** « date_naissance » → « dateNaissance » (propriété, variable). */
    public static function propertyName(string $name): string
    {
        $words = self::words($name);
        if ($words === []) {
            return 'c' . substr(md5($name), 0, 6);
        }
        $property = $words[0] . implode('', array_map(ucfirst(...), array_slice($words, 1)));
        if (ctype_digit($property[0])) {
            $property = 'c' . $property;
        }

        return $property === 'this' ? 'thisValue' : $property;
    }

    /** « gestion_courrier » → « gestion-courrier » (segment d'URL et dossier de vues). */
    public static function slug(string $name): string
    {
        $slug = implode('-', self::words($name));

        return $slug !== '' ? $slug : 't-' . substr(md5($name), 0, 6);
    }

    /** « bon_commande » → « Bon commande » (titre de table, sans retrait de « id »). */
    public static function title(string $name): string
    {
        $words = preg_split('/[_\s-]+|(?<=[a-z0-9])(?=[A-Z])/u', $name, -1, PREG_SPLIT_NO_EMPTY) ?: [$name];
        $title = implode(' ', array_map(mb_strtolower(...), $words));

        return mb_strtoupper(mb_substr($title, 0, 1)) . mb_substr($title, 1);
    }

    /** « date_naissance » → « Date naissance » ; « id_client » → « Client » (libellé de formulaire). */
    public static function label(string $name): string
    {
        $words = preg_split('/[_\s-]+|(?<=[a-z0-9])(?=[A-Z])/u', $name, -1, PREG_SPLIT_NO_EMPTY) ?: [$name];
        $words = array_map(mb_strtolower(...), $words);
        if (count($words) > 1 && in_array($words[0], ['id', 'fk'], true)) {
            array_shift($words);
        } elseif (count($words) > 1 && end($words) === 'id') {
            array_pop($words);
        }
        $label = implode(' ', $words);

        return mb_strtoupper(mb_substr($label, 0, 1)) . mb_substr($label, 1);
    }

    private static function ascii(string $value): string
    {
        if (class_exists(\Transliterator::class)) {
            $t = \Transliterator::create('Any-Latin; Latin-ASCII');
            if ($t !== null) {
                $converted = $t->transliterate($value);
                if ($converted !== false) {
                    return $converted;
                }
            }
        }
        $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);

        return $converted !== false ? $converted : $value;
    }
}
