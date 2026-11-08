<?php

declare(strict_types=1);

namespace App\Entity;

/**
 * Enregistrement de la table « pays ».
 *
 * Généré par gene à partir de la table « pays ».
 * Vous pouvez modifier ce fichier : une nouvelle génération ne l'écrasera pas sans --force.
 */
final class Pays implements \App\Framework\Entity
{
    /** Champs saisissables : libellé, type, contraintes. Lu par App\Framework\Form pour valider. */
    public const FIELDS = [
        'code' => [
            'label' => 'Code',
            'type' => 'string',
            'required' => true,
            'nullable' => false,
            'maxLength' => 2,
        ],
        'libelle' => [
            'label' => 'Libelle',
            'type' => 'string',
            'required' => true,
            'nullable' => false,
            'maxLength' => 80,
        ],
    ];

    /** code : char(2), clé primaire */
    public ?string $code = null;

    /** libelle : varchar(80) */
    public ?string $libelle = null;

    public static function fromRow(array $row): static
    {
        $entity = new self();
        $entity->code = isset($row['code']) ? (string) $row['code'] : null;
        $entity->libelle = isset($row['libelle']) ? (string) $row['libelle'] : null;

        return $entity;
    }

    public function toRow(): array
    {
        return [
            'code' => $this->code,
            'libelle' => $this->libelle,
        ];
    }
}
