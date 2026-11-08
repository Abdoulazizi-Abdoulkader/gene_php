<?php

declare(strict_types=1);

use App\Controller;
use App\Framework\Router;

/*
 * Routes générées par gene : ne modifiez pas ce fichier, il est régénéré avec le schéma.
 * Vos routes vont dans routes.custom.php (lues en premier : elles peuvent aussi remplacer celles-ci).
 */
return static function (Router $router): void {
    (require __DIR__ . '/routes.custom.php')($router);

    $router->get('/', [Controller\HomeController::class, 'index']);
    $router->crud('/client', Controller\ClientController::class);
    $router->crud('/commande', Controller\CommandeController::class);
    $router->crud('/ligne-commande', Controller\LigneCommandeController::class, readOnly: true);
    $router->crud('/pays', Controller\PaysController::class);
};
