<?php

declare(strict_types=1);

namespace App\Framework;

/**
 * Configuration : variables d'environnement réelles > fichier .env > valeurs par défaut de config/config.php.
 */
final class Config
{
    /** @param array<string, mixed> $values */
    public function __construct(private readonly array $values) {}

    public static function load(string $rootDir): self
    {
        $env = [];
        $envFile = $rootDir . '/.env';
        if (is_file($envFile)) {
            $env = self::parseEnv((string) file_get_contents($envFile));
        }
        foreach (getenv() as $key => $value) {
            $env[$key] = $value;
        }

        $factory = require $rootDir . '/config/config.php';

        return new self($factory($env));
    }

    /** Lecture en notation pointée : get('db.dsn'). */
    public function get(string $key, mixed $default = null): mixed
    {
        $value = $this->values;
        foreach (explode('.', $key) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    /**
     * Analyse un fichier .env : « CLE=valeur », commentaires « # », guillemets simples (littéral)
     * ou doubles (avec \n, \" et \\), préfixe « export » toléré.
     *
     * @return array<string, string>
     */
    public static function parseEnv(string $content): array
    {
        $result = [];
        foreach (preg_split('/\R/', $content) ?: [] as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (preg_match('/^(?:export\s+)?([A-Za-z_][A-Za-z0-9_]*)\s*=\s*(.*)$/', $line, $m) !== 1) {
                continue;
            }
            $value = $m[2];
            if (preg_match('/^"((?:[^"\\\\]|\\\\.)*)"/', $value, $q) === 1) {
                $value = strtr($q[1], ['\\n' => "\n", '\\"' => '"', '\\\\' => '\\']);
            } elseif (preg_match("/^'([^']*)'/", $value, $q) === 1) {
                $value = $q[1];
            } else {
                $value = trim((string) preg_replace('/\s+#.*$/', '', $value));
            }
            $result[$m[1]] = $value;
        }

        return $result;
    }
}
