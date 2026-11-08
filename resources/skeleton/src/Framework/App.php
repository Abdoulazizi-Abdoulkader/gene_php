<?php

declare(strict_types=1);

namespace App\Framework;

use PDO;

/**
 * Noyau de l'application : configuration, services paresseux, routage et gestion des erreurs.
 * Les contrôleurs reçoivent l'instance et y prennent ce dont ils ont besoin.
 */
final class App
{
    private ?PDO $pdo = null;
    private ?Session $session = null;
    private ?Csrf $csrf = null;
    private ?View $view = null;

    public function __construct(
        public readonly string $rootDir,
        public readonly Config $config,
        public readonly Request $request,
    ) {}

    public static function boot(string $rootDir): self
    {
        $config = Config::load($rootDir);
        $basePath = $config->get('app.base_path');

        return new self($rootDir, $config, Request::fromGlobals(is_string($basePath) && $basePath !== '' ? $basePath : null));
    }

    public function run(): void
    {
        $this->handle()->send();
    }

    public function handle(): Response
    {
        try {
            $router = new Router();
            $routes = require $this->rootDir . '/config/routes.php';
            $routes($router);

            [$handler, $params] = $router->match($this->request->method, $this->request->path);

            // Toute requête qui modifie des données doit porter le jeton CSRF de la session.
            if ($this->request->method === 'POST' && !$this->csrf()->isValid($this->request->post('_token'))) {
                throw new HttpException(419, 'Le formulaire a expiré. Rechargez la page puis réessayez.');
            }

            [$class, $method] = $handler;
            $controller = new $class($this);

            return $controller->{$method}(...$params);
        } catch (HttpException $e) {
            return $this->errorResponse($e->status, $e->getMessage(), null);
        } catch (\Throwable $e) {
            error_log(sprintf('[%s] %s in %s:%d', $e::class, $e->getMessage(), $e->getFile(), $e->getLine()));

            return $this->errorResponse(500, 'Une erreur interne est survenue.', $this->isDebug() ? $e : null);
        }
    }

    public function isDebug(): bool
    {
        return (bool) $this->config->get('app.debug', false);
    }

    public function pdo(): PDO
    {
        return $this->pdo ??= Database::connect(
            (string) $this->config->get('db.dsn', ''),
            (string) $this->config->get('db.user', ''),
            (string) $this->config->get('db.password', ''),
        );
    }

    public function session(): Session
    {
        return $this->session ??= new Session($this->request->isSecure());
    }

    public function csrf(): Csrf
    {
        return $this->csrf ??= new Csrf($this->session());
    }

    public function view(): View
    {
        if ($this->view === null) {
            $this->view = new View($this->rootDir . '/templates', $this->request, $this->csrf(), $this->session());
            $this->view->share('menu', $this->menu());
            $this->view->share('appName', (string) $this->config->get('app.name', 'Application'));
        }

        return $this->view;
    }

    /** @return list<array{label: string, path: string}> */
    private function menu(): array
    {
        $menu = is_file($this->rootDir . '/config/menu.php') ? require $this->rootDir . '/config/menu.php' : [];
        $custom = $this->rootDir . '/config/menu.custom.php';

        return is_file($custom) ? (require $custom)($menu) : $menu;
    }

    private function errorResponse(int $status, string $message, ?\Throwable $error): Response
    {
        try {
            $body = $this->view()->render('error', ['status' => $status, 'message' => $message, 'error' => $error]);
        } catch (\Throwable) {
            $body = '<!doctype html><meta charset="utf-8"><title>Erreur</title><h1>Erreur ' . $status . '</h1><p>'
                . htmlspecialchars($message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</p>';
        }

        return new Response($body, $status);
    }
}
