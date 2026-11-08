<?php

declare(strict_types=1);

namespace App\Framework;

/** Session PHP démarrée à la demande, avec des cookies durcis. */
final class Session
{
    public function __construct(private readonly bool $secure = false) {}

    public function get(string $key, mixed $default = null): mixed
    {
        $this->start();

        return $_SESSION[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $this->start();
        $_SESSION[$key] = $value;
    }

    public function remove(string $key): void
    {
        $this->start();
        unset($_SESSION[$key]);
    }

    /** Message affiché une seule fois, à la page suivante. Types : success, danger, warning, info. */
    public function flash(string $type, string $message): void
    {
        $this->start();
        $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
    }

    /** @return list<array{type: string, message: string}> */
    public function consumeFlashes(): array
    {
        $this->start();
        $flashes = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);

        return is_array($flashes) ? array_values($flashes) : [];
    }

    private function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        if (PHP_SAPI === 'cli') { // tests : pas d'en-têtes, session simulée
            $_SESSION ??= [];

            return;
        }
        session_start([
            'cookie_httponly' => true,
            'cookie_samesite' => 'Lax',
            'cookie_secure' => $this->secure,
            'use_strict_mode' => true,
            'use_only_cookies' => true,
        ]);
    }
}
