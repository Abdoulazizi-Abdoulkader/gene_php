<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity;

/**
 * Accès à la table « commande ». Les requêtes sont dans App\Framework\Repository ;
 * ajoutez ici vos requêtes spécifiques.
 *
 * Généré par gene à partir de la table « commande ».
 * Vous pouvez modifier ce fichier : une nouvelle génération ne l'écrasera pas sans --force.
 *
 * @extends \App\Framework\Repository<Entity\Commande>
 */
final class CommandeRepository extends \App\Framework\Repository
{
    protected const TABLE = 'commande';
    protected const PRIMARY_KEY = 'id';
    protected const AUTO_INCREMENT = true;
    protected const COLUMNS = ['id', 'client_id', 'passee_le', 'heure_livraison', 'montant_ttc'];
    protected const WRITABLE = ['client_id', 'passee_le', 'heure_livraison', 'montant_ttc'];
    protected const KEEP_IF_NULL = [];
    protected const RELATIONS = [
        'client_id' => ['client', 'id', 'nom'],
    ];
    protected const LABEL_COLUMN = 'id';
    protected const SEARCHABLE = [];

    protected function entityClass(): string
    {
        return Entity\Commande::class;
    }
}
