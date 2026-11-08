<?php

declare(strict_types=1);

namespace Gene\Web;

use Gene\Generator\GenerationOptions;
use Gene\Generator\Generator;
use Gene\Generator\Project;
use Gene\Generator\WriteAction;
use Gene\Generator\WriteReport;
use Gene\Naming\Inflector;
use Gene\Schema\ConnectionError;
use Gene\Schema\ConnectionSettings;
use Gene\Schema\Schema;

/**
 * Interface web de gene : un formulaire de connexion qui pilote le même moteur que la CLI.
 * Outil de développement : il refuse par défaut toute requête qui ne vient pas de la machine locale.
 */
final class WebApp
{
    /** Fichiers statiques servis depuis le squelette (liste blanche). */
    private const ASSETS = [
        '/assets/css/bootstrap.min.css' => ['css/bootstrap.min.css', 'text/css'],
        '/assets/js/bootstrap.bundle.min.js' => ['js/bootstrap.bundle.min.js', 'text/javascript'],
    ];

    public function __construct(private readonly string $projectDir) {}

    public function run(): void
    {
        $path = (string) (parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?? '/');
        $this->securityHeaders();

        if (!$this->isAllowedClient()) {
            $this->plain(403, "Interface réservée à la machine locale. Définissez GENE_WEB_ALLOW_REMOTE=1 pour l'ouvrir (déconseillé).");

            return;
        }
        if (isset(self::ASSETS[$path])) {
            [$file, $type] = self::ASSETS[$path];
            header('Content-Type: ' . $type);
            header('Cache-Control: public, max-age=86400');
            readfile($this->projectDir . '/resources/skeleton/public/assets/' . $file);

            return;
        }

        session_start(['cookie_httponly' => true, 'cookie_samesite' => 'Strict', 'use_strict_mode' => true]);
        $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
        $csrf = (string) $_SESSION['csrf'];

        if (isset($_GET['download']) && is_string($_GET['download'])) {
            $this->download($_GET['download']);

            return;
        }

        $form = ['host' => '127.0.0.1', 'port' => '3306', 'database' => '', 'user' => 'root', 'app' => '', 'tables' => '', 'force' => false];
        $result = null;
        $error = null;

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            foreach (['host', 'port', 'database', 'user', 'app', 'tables'] as $key) {
                $form[$key] = trim(is_string($_POST[$key] ?? null) ? $_POST[$key] : '');
            }
            $form['force'] = isset($_POST['force']);
            $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';

            if (!hash_equals($csrf, is_string($_POST['_token'] ?? null) ? $_POST['_token'] : '')) {
                $error = 'Le formulaire a expiré : rechargez la page.';
            } else {
                try {
                    $result = $this->generate($form, $password);
                } catch (\PDOException $e) {
                    $error = 'Connexion ou lecture de la base impossible : ' . ConnectionError::explain($e);
                } catch (\Throwable $e) {
                    $error = $e->getMessage();
                }
            }
        }

        header('Content-Type: text/html; charset=UTF-8');
        echo $this->page($form, $csrf, $result, $error);
    }

    /**
     * @param array{host: string, port: string, database: string, user: string, app: string, tables: string, force: bool} $form
     *
     * @return array{app: string, dir: string, report: WriteReport, warnings: list<string>, tables: int}
     */
    private function generate(array $form, string $password): array
    {
        if ($form['database'] === '') {
            throw new \InvalidArgumentException('Indiquez le nom de la base.');
        }
        $settings = new ConnectionSettings($form['host'], $form['port'], $form['database'], $form['user'], $password);
        $appName = $form['app'] !== '' ? $form['app'] : Inflector::className($form['database']);
        $options = new GenerationOptions($appName, $settings->dsn(), $form['user']); // valide le nom (pas de « / », « .. »)
        $schema = $settings->readSchema()->filter(Schema::parseTableList($form['tables']));

        $outcome = (new Project(new Generator($this->projectDir . '/resources')))
            ->generate($schema, $options, $this->outputDir() . '/' . $appName, false, $form['force']);

        return ['app' => $appName, 'dir' => $outcome->outputDir, 'report' => $outcome->report, 'warnings' => $outcome->result->warnings, 'tables' => $outcome->result->entityCount];
    }

    private function download(string $app): void
    {
        $dir = $this->outputDir() . '/' . $app;
        if (!GenerationOptions::isValidAppName($app) || !is_dir($dir)) {
            $this->plain(404, 'Application introuvable.');

            return;
        }
        if (!class_exists(\ZipArchive::class)) {
            $this->plain(500, 'Extension zip absente : récupérez le dossier ' . $dir . ' directement.');

            return;
        }
        $zipFile = (string) tempnam(sys_get_temp_dir(), 'gene');
        $zip = new \ZipArchive();
        $zip->open($zipFile, \ZipArchive::OVERWRITE);
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS));
        foreach ($files as $file) {
            /** @var \SplFileInfo $file */
            $relative = substr($file->getPathname(), strlen($dir) + 1);
            if ($file->isFile() && !$file->isLink() && $relative !== '.env') { // .env reste local : il peut contenir le mot de passe saisi depuis
                $zip->addFile($file->getPathname(), $app . '/' . str_replace('\\', '/', $relative));
            }
        }
        $zip->close();

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $app . '.zip"');
        header('Content-Length: ' . (string) filesize($zipFile));
        readfile($zipFile);
        unlink($zipFile);
    }

    private function outputDir(): string
    {
        $dir = getenv('GENE_OUTPUT_DIR');

        return is_string($dir) && $dir !== '' ? rtrim($dir, '/') : $this->projectDir . '/output';
    }

    private function isAllowedClient(): bool
    {
        if (getenv('GENE_WEB_ALLOW_REMOTE') === '1') {
            return true;
        }
        $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? '');

        return $ip === '127.0.0.1' || $ip === '::1' || str_starts_with($ip, '127.');
    }

    private function securityHeaders(): void
    {
        header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self'; script-src 'self'; form-action 'self'; frame-ancestors 'none'; base-uri 'none'");
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: no-referrer');
    }

    private function plain(int $status, string $message): void
    {
        http_response_code($status);
        header('Content-Type: text/plain; charset=UTF-8');
        echo $message;
    }

    private static function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * @param array{host: string, port: string, database: string, user: string, app: string, tables: string, force: bool} $form
     * @param array{app: string, dir: string, report: WriteReport, warnings: list<string>, tables: int}|null $result
     */
    private function page(array $form, string $csrf, ?array $result, ?string $error): string
    {
        $e = self::e(...);
        $field = static fn(string $name, string $label, string $value, string $type = 'text', string $extra = ''): string => sprintf(
            '<div class="col-md-6"><label class="form-label" for="%1$s">%2$s</label><input class="form-control" type="%3$s" id="%1$s" name="%1$s" value="%4$s" %5$s></div>',
            $name,
            $label,
            $type,
            $e($value),
            $extra,
        );

        $alert = $error !== null ? '<div class="alert alert-danger">' . $e($error) . '</div>' : '';
        $report = '';
        if ($result !== null) {
            $warnings = implode('', array_map(static fn(string $w): string => '<li>' . self::e($w) . '</li>', $result['warnings']));
            $summary = implode('', array_map(
                static fn(string $k, int $v): string => '<span class="badge text-bg-secondary me-1">' . self::e($k) . ' : ' . $v . '</span>',
                array_keys($result['report']->summary()),
                $result['report']->summary(),
            ));
            $skipped = $result['report']->paths(WriteAction::Skipped);
            $skippedHtml = $skipped === [] ? '' : '<p class="mb-1">Fichiers modifiés à la main, conservés (cochez « Écraser » pour les remplacer) :</p><ul class="small">'
                . implode('', array_map(static fn(string $p): string => '<li><code>' . self::e($p) . '</code></li>', $skipped)) . '</ul>';
            $report = sprintf(
                '<div class="alert alert-success"><h2 class="h5">%s généré (%d table(s))</h2><p class="mb-2">%s</p>'
                . '<p class="mb-2">Dossier : <code>%s</code></p>%s%s'
                . '<p class="mb-1"><strong>Lancer :</strong> renseignez <code>DB_PASSWORD</code> dans <code>.env</code>, puis <code>php -S localhost:8080 -t %s/public</code></p>'
                . '<a class="btn btn-sm btn-success mt-2" href="?download=%s">Télécharger le zip</a></div>',
                $e($result['app']),
                $result['tables'],
                $summary,
                $e($result['dir']),
                $warnings !== '' ? '<ul class="small mb-2">' . $warnings . '</ul>' : '',
                $skippedHtml,
                $e($result['dir']),
                rawurlencode($result['app']),
            );
        }

        return '<!doctype html><html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">'
            . '<title>gene — générateur CRUD PHP</title><link rel="stylesheet" href="/assets/css/bootstrap.min.css"></head>'
            . '<body class="bg-body-tertiary"><main class="container py-5"><div class="row justify-content-center"><div class="col-lg-8">'
            . '<h1 class="h3 mb-1">gene</h1><p class="text-body-secondary mb-4">Génère une application CRUD PHP 8.3+ à partir d\'une base MySQL/MariaDB.</p>'
            . $alert . $report
            . '<form method="post" class="card"><div class="card-body"><input type="hidden" name="_token" value="' . $e($csrf) . '"><div class="row g-3">'
            . $field('host', 'Hôte', $form['host'], 'text', 'required')
            . $field('port', 'Port', $form['port'], 'text', 'required inputmode="numeric" pattern="[0-9]+"')
            . $field('database', 'Base de données', $form['database'], 'text', 'required')
            . $field('user', 'Utilisateur', $form['user'], 'text', 'required autocomplete="username"')
            . $field('password', 'Mot de passe', '', 'password', 'autocomplete="current-password"')
            . $field('app', 'Nom de l\'application', $form['app'], 'text', 'pattern="[A-Za-z][A-Za-z0-9_\-]{0,63}" placeholder="dérivé de la base"')
            . '<div class="col-12"><label class="form-label" for="tables">Tables (facultatif)</label><input class="form-control" id="tables" name="tables" value="' . $e($form['tables']) . '" placeholder="client, commande — vide : toutes"></div>'
            . '<div class="col-12 form-check ms-1"><input class="form-check-input" type="checkbox" id="force" name="force" value="1"' . ($form['force'] ? ' checked' : '') . '>'
            . '<label class="form-check-label" for="force">Écraser aussi les fichiers modifiés à la main</label></div>'
            . '</div></div><div class="card-footer text-end"><button class="btn btn-primary" type="submit">Générer</button></div></form>'
            . '<p class="small text-body-secondary mt-3">Le mot de passe sert uniquement à lire le schéma : il n\'est ni enregistré ni écrit dans les fichiers générés.</p>'
            . '</div></div></main></body></html>';
    }
}
