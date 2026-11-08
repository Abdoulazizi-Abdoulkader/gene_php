<?php

declare(strict_types=1);

namespace App\Entity;

/**
 * Enregistrement de la table « client ».
 *
 * Généré par gene à partir de la table « client ».
 * Vous pouvez modifier ce fichier : une nouvelle génération ne l'écrasera pas sans --force.
 */
final class Client implements \App\Framework\Entity
{
    /** Champs saisissables : libellé, type, contraintes. Lu par App\Framework\Form pour valider. */
    public const FIELDS = [
        'nom' => [
            'label' => 'Nom',
            'type' => 'string',
            'required' => true,
            'nullable' => false,
            'maxLength' => 100,
        ],
        'email' => [
            'label' => 'Email',
            'type' => 'string',
            'required' => false,
            'nullable' => true,
            'maxLength' => 190,
        ],
        'date_naissance' => [
            'label' => 'Date naissance',
            'type' => 'date',
            'required' => false,
            'nullable' => true,
        ],
        'actif' => [
            'label' => 'Actif',
            'type' => 'bool',
            'required' => false,
            'nullable' => false,
            'default' => '1',
        ],
        'solde' => [
            'label' => 'Solde',
            'type' => 'decimal',
            'required' => false,
            'nullable' => false,
            'default' => '0.00',
            'precision' => 10,
            'scale' => 2,
        ],
        'statut' => [
            'label' => 'Statut',
            'type' => 'enum',
            'required' => false,
            'nullable' => false,
            'choices' => ['prospect', 'client', 'ancien'],
            'default' => 'prospect',
        ],
        'pays_code' => [
            'label' => 'Pays',
            'type' => 'string',
            'required' => false,
            'nullable' => true,
            'maxLength' => 2,
        ],
        'parrain_id' => [
            'label' => 'Parrain',
            'type' => 'int',
            'required' => false,
            'nullable' => true,
            'min' => 0,
            'max' => 4294967295,
        ],
        'notes' => [
            'label' => 'Notes',
            'type' => 'text',
            'required' => false,
            'nullable' => true,
            'maxBytes' => 65535,
        ],
        'cree_le' => [
            'label' => 'Cree le',
            'type' => 'datetime',
            'required' => false,
            'nullable' => false,
        ],
    ];

    /** id : int unsigned, clé primaire */
    public ?int $id = null;

    /** nom : varchar(100) */
    public ?string $nom = null;

    /** email : varchar(190), NULL */
    public ?string $email = null;

    /** date_naissance : date, NULL */
    public ?string $dateNaissance = null;

    /** actif : tinyint(1) */
    public ?bool $actif = null;

    /** solde : decimal(10,2) */
    public ?string $solde = null;

    /** statut : enum('prospect','client','ancien') */
    public ?string $statut = null;

    /** pays_code : char(2), NULL, → pays */
    public ?string $paysCode = null;

    /** parrain_id : int unsigned, NULL, → client */
    public ?int $parrainId = null;

    /** notes : text, NULL */
    public ?string $notes = null;

    /** cree_le : datetime */
    public ?string $creeLe = null;

    public static function fromRow(array $row): static
    {
        $entity = new self();
        $entity->id = isset($row['id']) ? (int) $row['id'] : null;
        $entity->nom = isset($row['nom']) ? (string) $row['nom'] : null;
        $entity->email = isset($row['email']) ? (string) $row['email'] : null;
        $entity->dateNaissance = isset($row['date_naissance']) ? (string) $row['date_naissance'] : null;
        $entity->actif = isset($row['actif']) ? (bool) $row['actif'] : null;
        $entity->solde = isset($row['solde']) ? (string) $row['solde'] : null;
        $entity->statut = isset($row['statut']) ? (string) $row['statut'] : null;
        $entity->paysCode = isset($row['pays_code']) ? (string) $row['pays_code'] : null;
        $entity->parrainId = isset($row['parrain_id']) ? (int) $row['parrain_id'] : null;
        $entity->notes = isset($row['notes']) ? (string) $row['notes'] : null;
        $entity->creeLe = isset($row['cree_le']) ? (string) $row['cree_le'] : null;

        return $entity;
    }

    public function toRow(): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'email' => $this->email,
            'date_naissance' => $this->dateNaissance,
            'actif' => $this->actif,
            'solde' => $this->solde,
            'statut' => $this->statut,
            'pays_code' => $this->paysCode,
            'parrain_id' => $this->parrainId,
            'notes' => $this->notes,
            'cree_le' => $this->creeLe,
        ];
    }
}
