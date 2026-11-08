<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity;

/**
 * Accès à la table « pays ». Les requêtes sont dans App\Framework\Repository ;
 * ajoutez ici vos requêtes spécifiques.
 *
 * Généré par gene à partir de la table « pays ».
 * Vous pouvez modifier ce fichier : une nouvelle génération ne l'écrasera pas sans --force.
 *
 * @extends \App\Framework\Repository<Entity\Pays>
 */
final class PaysRepository extends \App\Framework\Repository
{
    protected const TABLE = 'pays';
    protected const PRIMARY_KEY = 'code';
    protected const AUTO_INCREMENT = false;
    protected const COLUMNS = ['code', 'libelle'];
    protected const WRITABLE = ['code', 'libelle'];
    protected const KEEP_IF_NULL = [];
    protected const RELATIONS = [];
    protected const LABEL_COLUMN = 'libelle';
    protected const SEARCHABLE = ['code', 'libelle'];

    protected function entityClass(): string
    {
        return Entity\Pays::class;
    }
}
