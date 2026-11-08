<?php

declare(strict_types=1);

namespace App\Framework;

/** Contrat des entités générées : conversion depuis et vers une ligne SQL (clé = nom de colonne). */
interface Entity
{
    /** @param array<string, mixed> $row */
    public static function fromRow(array $row): static;

    /** @return array<string, mixed> */
    public function toRow(): array;
}
