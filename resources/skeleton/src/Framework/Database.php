<?php

declare(strict_types=1);

namespace App\Framework;

use PDO;

final class Database
{
    public static function connect(string $dsn, string $user, string $password): PDO
    {
        if ($dsn === '') {
            throw new \RuntimeException('DB_DSN n\'est pas défini : copiez .env.example vers .env et renseignez la connexion.');
        }
        if (str_starts_with($dsn, 'mysql:') && !str_contains($dsn, 'charset=')) {
            $dsn .= ';charset=utf8mb4';
        }

        return new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false,
        ]);
    }

    /** Cite un identifiant SQL (MySQL/MariaDB). Ne s'applique qu'à des noms connus, jamais à une saisie. */
    public static function quote(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }

    /** Violation de contrainte (doublon, clé étrangère…), SQLSTATE 23000. */
    public static function isConstraintViolation(\PDOException $e): bool
    {
        return self::sqlState($e) === '23000';
    }

    /**
     * Message compréhensible pour une erreur due aux données saisies, null pour une vraie panne
     * (qui doit remonter en erreur 500).
     */
    public static function userMessage(\PDOException $e): ?string
    {
        return match (self::sqlState($e)) {
            '23000' => 'La base a refusé l\'enregistrement : valeur déjà utilisée (doublon) ou référence inexistante.',
            '22001' => 'Une valeur est trop longue pour la colonne qui doit la recevoir.',
            '22003' => 'Un nombre dépasse la capacité de sa colonne.',
            '22007', '22008' => 'Une date ou une heure est invalide pour la base.',
            'HY000' => str_contains($e->getMessage(), '1366') || str_contains($e->getMessage(), '1292')
                ? 'Une valeur saisie a un format refusé par la base.' : null,
            default => null,
        };
    }

    private static function sqlState(\PDOException $e): string
    {
        $state = is_array($e->errorInfo) ? ($e->errorInfo[0] ?? null) : null;

        return is_string($state) ? $state : (string) $e->getCode();
    }
}
