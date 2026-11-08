<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity;

/**
 * Accès à la table « ligne_commande ». Les requêtes sont dans App\Framework\Repository ;
 * ajoutez ici vos requêtes spécifiques.
 *
 * Généré par gene à partir de la table « ligne_commande ».
 * Vous pouvez modifier ce fichier : une nouvelle génération ne l'écrasera pas sans --force.
 *
 * @extends \App\Framework\Repository<Entity\LigneCommande>
 */
final class LigneCommandeRepository extends \App\Framework\Repository
{
    protected const TABLE = 'ligne_commande';
    protected const PRIMARY_KEY = null;
    protected const AUTO_INCREMENT = false;
    protected const COLUMNS = ['commande_id', 'numero', 'quantite'];
    protected const WRITABLE = ['commande_id', 'numero', 'quantite'];
    protected const KEEP_IF_NULL = [];
    protected const RELATIONS = [
        'commande_id' => ['commande', 'id', 'id'],
    ];
    protected const LABEL_COLUMN = 'commande_id';
    protected const SEARCHABLE = [];

    protected function entityClass(): string
    {
        return Entity\LigneCommande::class;
    }
}
