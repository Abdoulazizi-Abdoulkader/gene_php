<?php
/**
 * Mise en page commune.
 *
 * @var App\Framework\View $this
 * @var string $content
 * @var string $appName
 * @var list<array{label: string, path: string}> $menu
 */
$title = $this->pageTitle() !== null ? $this->pageTitle() . ' · ' . $appName : $appName;
$direct = count($menu) > 6 ? array_slice($menu, 0, 5) : $menu;
$more = count($menu) > 6 ? array_slice($menu, 5) : [];
?>
<!doctype html>
<html lang="fr" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->e($title) ?></title>
    <link rel="stylesheet" href="<?= $this->e($this->asset('css/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= $this->e($this->asset('css/app.css')) ?>">
    <script src="<?= $this->e($this->asset('js/bootstrap.bundle.min.js')) ?>" defer></script>
    <script src="<?= $this->e($this->asset('js/app.js')) ?>" defer></script>
</head>
<body class="d-flex flex-column min-vh-100">
<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom">
    <div class="container">
        <a class="navbar-brand fw-semibold" href="<?= $this->e($this->url('/')) ?>"><?= $this->e($appName) ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-nav"
                aria-controls="main-nav" aria-expanded="false" aria-label="Afficher le menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="main-nav">
            <ul class="navbar-nav me-auto">
                <?php foreach ($direct as $item): ?>
                    <li class="nav-item">
                        <a class="nav-link<?= $this->isActive($item['path']) ? ' active' : '' ?>"
                           <?= $this->isActive($item['path']) ? 'aria-current="page"' : '' ?>
                           href="<?= $this->e($this->url($item['path'])) ?>"><?= $this->e($item['label']) ?></a>
                    </li>
                <?php endforeach ?>
                <?php if ($more !== []): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Autres tables</a>
                        <ul class="dropdown-menu">
                            <?php foreach ($more as $item): ?>
                                <li><a class="dropdown-item<?= $this->isActive($item['path']) ? ' active' : '' ?>"
                                       href="<?= $this->e($this->url($item['path'])) ?>"><?= $this->e($item['label']) ?></a></li>
                            <?php endforeach ?>
                        </ul>
                    </li>
                <?php endif ?>
            </ul>
        </div>
    </div>
</nav>

<main class="container py-4 flex-grow-1">
    <?php foreach ($this->flashes() as $flash): ?>
        <div class="alert alert-<?= $this->e($flash['type']) ?> alert-dismissible fade show" role="alert">
            <?= $this->e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    <?php endforeach ?>

    <?= $content ?>
</main>

<footer class="border-top py-3 text-center small text-body-secondary">
    <?= $this->e($appName) ?> — généré par gene
</footer>
</body>
</html>
