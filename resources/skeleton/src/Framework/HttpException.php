<?php

declare(strict_types=1);

namespace App\Framework;

final class HttpException extends \RuntimeException
{
    public function __construct(public readonly int $status, string $message = '')
    {
        parent::__construct($message !== '' ? $message : match ($status) {
            404 => 'Page introuvable.',
            405 => 'Méthode non autorisée.',
            default => 'Erreur.',
        }, $status);
    }
}
