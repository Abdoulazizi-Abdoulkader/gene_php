<?php

declare(strict_types=1);

namespace App\Entity;

/**
 * Enregistrement de la table « commande ».
 *
 * Généré par gene à partir de la table « commande ».
 * Vous pouvez modifier ce fichier : une nouvelle génération ne l'écrasera pas sans --force.
 */
final class Commande implements \App\Framework\Entity
{
    /** Champs saisissables : libellé, type, contraintes. Lu par App\Framework\Form pour valider. */
    public const FIELDS = [
        'client_id' => [
            'label' => 'Client',
            'type' => 'int',
            'required' => true,
            'nullable' => false,
            'min' => 0,
            'max' => 4294967295,
        ],
        'passee_le' => [
            'label' => 'Passee le',
            'type' => 'datetime',
            'required' => true,
            'nullable' => false,
        ],
        'heure_livraison' => [
            'label' => 'Heure livraison',
            'type' => 'time',
            'required' => false,
            'nullable' => true,
        ],
        'montant_ttc' => [
            'label' => 'Montant ttc',
            'type' => 'float',
            'required' => false,
            'nullable' => false,
            'default' => '0',
        ],
    ];

    /** id : int, clé primaire */
    public ?int $id = null;

    /** client_id : int unsigned, → client */
    public ?int $clientId = null;

    /** passee_le : datetime */
    public ?string $passeeLe = null;

    /** heure_livraison : time, NULL */
    public ?string $heureLivraison = null;

    /** montant_ttc : double */
    public ?float $montantTtc = null;

    /** pièce_jointe : blob, NULL */
    public ?string $pieceJointe = null;

    public static function fromRow(array $row): static
    {
        $entity = new self();
        $entity->id = isset($row['id']) ? (int) $row['id'] : null;
        $entity->clientId = isset($row['client_id']) ? (int) $row['client_id'] : null;
        $entity->passeeLe = isset($row['passee_le']) ? (string) $row['passee_le'] : null;
        $entity->heureLivraison = isset($row['heure_livraison']) ? (string) $row['heure_livraison'] : null;
        $entity->montantTtc = isset($row['montant_ttc']) ? (float) $row['montant_ttc'] : null;
        $entity->pieceJointe = isset($row['pièce_jointe']) ? (string) $row['pièce_jointe'] : null;

        return $entity;
    }

    public function toRow(): array
    {
        return [
            'id' => $this->id,
            'client_id' => $this->clientId,
            'passee_le' => $this->passeeLe,
            'heure_livraison' => $this->heureLivraison,
            'montant_ttc' => $this->montantTtc,
            'pièce_jointe' => $this->pieceJointe,
        ];
    }
}
