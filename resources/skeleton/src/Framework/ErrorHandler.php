<?php

declare(strict_types=1);

namespace App\Framework;

/**
 * Transforme avertissements et notices en exceptions (un bogue ne passe plus inaperçu)
 * et affiche une page générique si une erreur survient avant que App ne prenne le relais.
 */
final class ErrorHandler
{
    public static function register(): void
    {
        set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
            if ((error_reporting() & $severity) === 0) {
                return false;
            }

            throw new \ErrorException($message, 0, $severity, $file, $line);
        });

        set_exception_handler(static function (\Throwable $e): void {
            error_log(sprintf('[%s] %s in %s:%d', $e::class, $e->getMessage(), $e->getFile(), $e->getLine()));
            if (!headers_sent()) {
                http_response_code(500);
                header('Content-Type: text/html; charset=UTF-8');
            }
            $debug = filter_var(getenv('APP_DEBUG') ?: ($_ENV['APP_DEBUG'] ?? false), FILTER_VALIDATE_BOOL);
            echo '<!doctype html><meta charset="utf-8"><title>Erreur</title><h1>Erreur interne</h1>';
            if ($debug) {
                echo '<pre>' . htmlspecialchars((string) $e, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</pre>';
            }
        });
    }
}
