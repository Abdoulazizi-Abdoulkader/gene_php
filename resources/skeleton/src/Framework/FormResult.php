<?php

declare(strict_types=1);

namespace App\Framework;

final readonly class FormResult
{
    /**
     * @param array<string, mixed>  $values valeurs converties (prêtes pour Entity::fromRow)
     * @param array<string, string> $errors message par champ
     * @param array<string, string> $raw    saisie nettoyée, pour réafficher le formulaire
     */
    public function __construct(
        public array $values,
        public array $errors,
        public array $raw,
    ) {}

    public function isValid(): bool
    {
        return $this->errors === [];
    }
}
