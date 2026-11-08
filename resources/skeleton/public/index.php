<?php

declare(strict_types=1);

// Serveur intégré (php -S) : laisser servir directement les fichiers statiques existants.
if (PHP_SAPI === 'cli-server') {
    $file = __DIR__ . (string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
    if ($file !== __FILE__ && is_file($file)) {
        return false;
    }
}

$root = dirname(__DIR__);

if (is_file($root . '/vendor/autoload.php')) {
    require $root . '/vendor/autoload.php';
} else {
    // Sans Composer : chargement PSR-4 minimal de l'espace de noms App\ depuis src/.
    spl_autoload_register(static function (string $class) use ($root): void {
        if (str_starts_with($class, 'App\\')) {
            $file = $root . '/src/' . str_replace('\\', '/', substr($class, 4)) . '.php';
            if (is_file($file)) {
                require $file;
            }
        }
    });
}

App\Framework\ErrorHandler::register();
App\Framework\App::boot($root)->run();
