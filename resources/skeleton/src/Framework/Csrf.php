<?php

declare(strict_types=1);

namespace App\Framework;

/** Jeton anti-CSRF par session, vérifié automatiquement par App pour chaque POST. */
final class Csrf
{
    private const KEY = '_csrf';

    public function __construct(private readonly Session $session) {}

    public function token(): string
    {
        $token = $this->session->get(self::KEY);
        if (!is_string($token) || $token === '') {
            $token = bin2hex(random_bytes(32));
            $this->session->set(self::KEY, $token);
        }

        return $token;
    }

    public function isValid(?string $submitted): bool
    {
        $expected = $this->session->get(self::KEY);

        return is_string($expected) && $expected !== '' && is_string($submitted) && hash_equals($expected, $submitted);
    }
}
