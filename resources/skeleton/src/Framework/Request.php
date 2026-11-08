<?php

declare(strict_types=1);

namespace App\Framework;

final readonly class Request
{
    /**
     * @param array<string, mixed> $query
     * @param array<string, mixed> $post
     * @param array<string, mixed> $server
     */
    public function __construct(
        public string $method,
        public string $path,
        public string $basePath,
        private array $query = [],
        private array $post = [],
        private array $server = [],
    ) {}

    /**
     * @param string|null $basePath préfixe d'URL imposé (APP_BASE_PATH) ; sinon déduit de SCRIPT_NAME
     */
    public static function fromGlobals(?string $basePath = null): self
    {
        $scriptName = (string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php');
        // Chemin laissé encodé : un identifiant contenant « %2F » doit rester un seul segment.
        // Le routeur décode les paramètres après correspondance.
        $uriPath = (string) (parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?? '/');

        [$base, $path] = self::splitPath($uriPath, $scriptName, $basePath);

        return new self(
            strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')),
            $path,
            $base,
            $_GET,
            $_POST,
            $_SERVER,
        );
    }

    /**
     * Sépare le préfixe de l'application du chemin routé. Gère trois déploiements :
     * racine web = public/ (php -S, vhost), sous-dossier Apache pointant sur public/,
     * ou sous-dossier Apache pointant sur la racine du projet (réécriture vers public/).
     *
     * Le préfixe est comparé sur le chemin décodé (SCRIPT_NAME l'est) ; le chemin routé, lui, reste encodé.
     *
     * @return array{0: string, 1: string} [préfixe sans « / » final, chemin encodé commençant par « / »]
     */
    public static function splitPath(string $uriPath, string $scriptName, ?string $forcedBase = null): array
    {
        $decoded = rawurldecode($uriPath);
        $candidates = [];
        if ($forcedBase !== null) {
            $candidates[] = rtrim($forcedBase, '/');
        } else {
            $dir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
            $candidates[] = $dir;
            if (str_ends_with($dir, '/public')) {
                $candidates[] = substr($dir, 0, -strlen('/public'));
            }
        }

        foreach ($candidates as $base) {
            if ($base === '' || $decoded === $base || str_starts_with($decoded, $base . '/')) {
                // Un nom de dossier ne contient pas « / » : retirer autant de segments que le préfixe en compte.
                $baseSegments = $base === '' ? 0 : count(explode('/', trim($base, '/')));
                $segments = array_slice(explode('/', trim($uriPath, '/')), $baseSegments);
                $path = '/' . trim(implode('/', $segments), '/');
                if ($path === '/index.php' || str_starts_with($path, '/index.php/')) {
                    $path = '/' . trim(substr($path, strlen('/index.php')), '/');
                }

                return [$base, $path];
            }
        }

        return ['', '/' . trim($uriPath, '/')];
    }

    public function query(string $key, ?string $default = null): ?string
    {
        $value = $this->query[$key] ?? null;

        return is_string($value) ? $value : $default;
    }

    public function queryInt(string $key, int $default, int $min = 1): int
    {
        $value = filter_var($this->query($key), FILTER_VALIDATE_INT);

        return is_int($value) && $value >= $min ? $value : $default;
    }

    public function post(string $key, ?string $default = null): ?string
    {
        $value = $this->post[$key] ?? null;

        return is_string($value) ? $value : $default;
    }

    /**
     * Champs POST scalaires uniquement (les tableaux sont ignorés).
     *
     * @return array<string, string>
     */
    public function postAll(): array
    {
        $result = [];
        foreach ($this->post as $key => $value) {
            if (is_string($value)) {
                $result[(string) $key] = $value;
            }
        }

        return $result;
    }

    public function isSecure(): bool
    {
        $https = $this->server['HTTPS'] ?? '';

        return ($https !== '' && $https !== 'off') || ($this->server['SERVER_PORT'] ?? null) === '443';
    }
}
