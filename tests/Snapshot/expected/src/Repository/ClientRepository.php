<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity;

/**
 * Accès à la table « client ». Les requêtes sont dans App\Framework\Repository ;
 * ajoutez ici vos requêtes spécifiques.
 *
 * Généré par gene à partir de la table « client ».
 * Vous pouvez modifier ce fichier : une nouvelle génération ne l'écrasera pas sans --force.
 *
 * @extends \App\Framework\Repository<Entity\Client>
 */
final class ClientRepository extends \App\Framework\Repository
{
    protected const TABLE = 'client';
    protected const PRIMARY_KEY = 'id';
    protected const AUTO_INCREMENT = true;
    protected const COLUMNS = [
        'id',
        'nom',
        'email',
        'date_naissance',
        'actif',
        'solde',
        'statut',
        'pays_code',
        'parrain_id',
        'notes',
        'cree_le',
    ];
    protected const WRITABLE = [
        'nom',
        'email',
        'date_naissance',
        'actif',
        'solde',
        'statut',
        'pays_code',
        'parrain_id',
        'notes',
        'cree_le',
    ];
    protected const KEEP_IF_NULL = ['cree_le'];
    protected const RELATIONS = [
        'pays_code' => ['pays', 'code', 'libelle'],
        'parrain_id' => ['client', 'id', 'nom'],
    ];
    protected const LABEL_COLUMN = 'nom';
    protected const SEARCHABLE = ['nom', 'email', 'statut', 'pays_code'];

    protected function entityClass(): string
    {
        return Entity\Client::class;
    }
}
