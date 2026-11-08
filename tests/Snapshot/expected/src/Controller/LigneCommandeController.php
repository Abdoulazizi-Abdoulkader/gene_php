<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity;
use App\Framework;
use App\Repository;

/**
 * Liste de la table « ligne_commande ».
 *
 * Généré par gene à partir de la table « ligne_commande ».
 * Vous pouvez modifier ce fichier : une nouvelle génération ne l'écrasera pas sans --force.
 */
final class LigneCommandeController extends Framework\Controller
{
    private const PATH = '/ligne-commande';

    private readonly Repository\LigneCommandeRepository $repository;

    public function __construct(Framework\App $app)
    {
        parent::__construct($app);
        $this->repository = new Repository\LigneCommandeRepository($app->pdo());
    }

    public function index(): Framework\Response
    {
        $request = $this->request();
        $page = $this->repository->paginate(
            $request->queryInt('page', 1),
            (int) $this->app->config->get('app.per_page', 20),
            $request->query('sort'),
            $request->query('dir') ?? 'asc',
            $request->query('q') ?? '',
        );

        return $this->render('ligne-commande/index', ['page' => $page, 'path' => self::PATH]);
    }
}
