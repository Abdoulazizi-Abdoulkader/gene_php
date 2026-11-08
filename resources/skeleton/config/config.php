<?php

declare(strict_types=1);

/*
 * Configuration de l'application. Les valeurs viennent de l'environnement
 * (variables réelles ou fichier .env) ; ne jamais écrire de secret ici.
 *
 * @param array<string, string> $env
 */
return static fn(array $env): array => [
    'app' => [
        'name' => $env['APP_NAME'] ?? 'Application',
        'env' => $env['APP_ENV'] ?? 'prod',
        'debug' => filter_var($env['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOL),
        // Préfixe d'URL imposé (ex. « /mon-app ») si la détection automatique ne convient pas.
        'base_path' => $env['APP_BASE_PATH'] ?? null,
        'per_page' => (int) ($env['APP_PER_PAGE'] ?? 20),
    ],
    'db' => [
        'dsn' => $env['DB_DSN'] ?? '',
        'user' => $env['DB_USER'] ?? '',
        'password' => $env['DB_PASSWORD'] ?? '',
    ],
];
