<?php

declare(strict_types=1);

namespace App\Framework;

/**
 * Routeur minimal : motifs « /client/{id} », première route correspondante gagnante.
 *
 * @phpstan-type Handler array{0: class-string<Controller>, 1: string}
 */
final class Router
{
    /** @var list<array{method: string, regex: string, handler: Handler}> */
    private array $routes = [];

    /** @param Handler $handler */
    public function get(string $pattern, array $handler): self
    {
        return $this->add('GET', $pattern, $handler);
    }

    /** @param Handler $handler */
    public function post(string $pattern, array $handler): self
    {
        return $this->add('POST', $pattern, $handler);
    }

    /**
     * Routes CRUD d'une ressource : liste, création, fiche, édition, suppression.
     *
     * @param class-string<Controller> $controller
     */
    public function crud(string $prefix, string $controller, bool $readOnly = false): self
    {
        $prefix = '/' . trim($prefix, '/');
        $this->get($prefix, [$controller, 'index']);
        if ($readOnly) {
            return $this;
        }

        return $this
            ->get($prefix . '/new', [$controller, 'create'])
            ->post($prefix, [$controller, 'store'])
            ->get($prefix . '/{id}', [$controller, 'show'])
            ->get($prefix . '/{id}/edit', [$controller, 'edit'])
            ->post($prefix . '/{id}', [$controller, 'update'])
            ->post($prefix . '/{id}/delete', [$controller, 'destroy']);
    }

    /** @param Handler $handler */
    public function add(string $method, string $pattern, array $handler): self
    {
        $pattern = '/' . trim($pattern, '/');
        $regex = preg_replace_callback(
            '/\\\\\{([a-zA-Z_][a-zA-Z0-9_]*)\\\\\}/',
            static fn(array $m): string => '(?P<' . $m[1] . '>[^/]+)',
            preg_quote($pattern, '#'),
        );
        $this->routes[] = ['method' => strtoupper($method), 'regex' => '#^' . $regex . '$#u', 'handler' => $handler];

        return $this;
    }

    /**
     * @return array{0: Handler, 1: array<string, string>}
     *
     * @throws HttpException 404 si aucun motif ne correspond, 405 si seule la méthode diffère
     */
    public function match(string $method, string $path): array
    {
        $method = $method === 'HEAD' ? 'GET' : strtoupper($method);
        $allowed = [];
        foreach ($this->routes as $route) {
            if (preg_match($route['regex'], $path, $m) !== 1) {
                continue;
            }
            if ($route['method'] !== $method) {
                $allowed[] = $route['method'];
                continue;
            }
            $params = array_map(rawurldecode(...), array_filter($m, static fn($k): bool => is_string($k), ARRAY_FILTER_USE_KEY));

            return [$route['handler'], $params];
        }

        throw $allowed === [] ? new HttpException(404) : new HttpException(405);
    }
}
