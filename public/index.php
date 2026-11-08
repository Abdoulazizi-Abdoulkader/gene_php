<?php

declare(strict_types=1);

// Interface web de gene. Lancement : php -S localhost:8000 -t public
$projectDir = dirname(__DIR__);
if (!is_file($projectDir . '/vendor/autoload.php')) {
    http_response_code(500);
    exit('Dépendances absentes : lancez « composer install ».');
}
require $projectDir . '/vendor/autoload.php';

(new Gene\Web\WebApp($projectDir))->run();
