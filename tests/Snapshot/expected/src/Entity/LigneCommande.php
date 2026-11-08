<?php

declare(strict_types=1);

namespace App\Entity;

/**
 * Enregistrement de la table « ligne_commande ».
 *
 * Généré par gene à partir de la table « ligne_commande ».
 * Vous pouvez modifier ce fichier : une nouvelle génération ne l'écrasera pas sans --force.
 */
final class LigneCommande implements \App\Framework\Entity
{
    /** Champs saisissables : libellé, type, contraintes. Lu par App\Framework\Form pour valider. */
    public const FIELDS = [
        'commande_id' => [
            'label' => 'Commande',
            'type' => 'int',
            'required' => true,
            'nullable' => false,
            'min' => -2147483648,
            'max' => 2147483647,
        ],
        'numero' => [
            'label' => 'Numero',
            'type' => 'int',
            'required' => true,
            'nullable' => false,
            'min' => -32768,
            'max' => 32767,
        ],
        'quantite' => [
            'label' => 'Quantite',
            'type' => 'int',
            'required' => false,
            'nullable' => false,
            'min' => -2147483648,
            'max' => 2147483647,
            'default' => '1',
        ],
    ];

    /** commande_id : int, clé primaire, → commande */
    public ?int $commandeId = null;

    /** numero : smallint, clé primaire */
    public ?int $numero = null;

    /** quantite : int */
    public ?int $quantite = null;

    public static function fromRow(array $row): static
    {
        $entity = new self();
        $entity->commandeId = isset($row['commande_id']) ? (int) $row['commande_id'] : null;
        $entity->numero = isset($row['numero']) ? (int) $row['numero'] : null;
        $entity->quantite = isset($row['quantite']) ? (int) $row['quantite'] : null;

        return $entity;
    }

    public function toRow(): array
    {
        return [
            'commande_id' => $this->commandeId,
            'numero' => $this->numero,
            'quantite' => $this->quantite,
        ];
    }
}
