<?php

declare(strict_types=1);

namespace App\Framework;

final class Response
{
    /** En-têtes de sécurité appliqués à chaque réponse (surchargeables). */
    private const SECURITY_HEADERS = [
        'Content-Security-Policy' => "default-src 'self'; img-src 'self' data:; style-src 'self'; script-src 'self'; form-action 'self'; frame-ancestors 'none'; base-uri 'self'",
        'X-Content-Type-Options' => 'nosniff',
        'Referrer-Policy' => 'same-origin',
        'X-Frame-Options' => 'DENY',
    ];

    /** @param array<string, string> $headers */
    public function __construct(
        public readonly string $body = '',
        public readonly int $status = 200,
        public readonly array $headers = ['Content-Type' => 'text/html; charset=UTF-8'],
    ) {}

    /** Redirection « See Other » : après un POST, le navigateur recharge la cible en GET. */
    public static function redirect(string $url, int $status = 303): self
    {
        return new self('', $status, ['Location' => $url]);
    }

    public function send(): void
    {
        if (!headers_sent()) {
            http_response_code($this->status);
            foreach ([...self::SECURITY_HEADERS, ...$this->headers] as $name => $value) {
                header($name . ': ' . $value);
            }
        }
        echo $this->body;
    }
}
