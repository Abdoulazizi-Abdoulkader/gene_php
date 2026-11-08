<?php

declare(strict_types=1);

use App\Framework\Router;

/*
 * Vos routes. gene crée ce fichier une fois et ne l'écrase jamais ensuite.
 * Elles sont enregistrées avant les routes générées : une route d'ici l'emporte sur la même URL générée.
 *
 * Exemple : $router->get('/rapport', [App\Controller\RapportController::class, 'index']);
 */
return static function (Router $router): void {};
