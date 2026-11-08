<?php
/**
 * @var App\Framework\View $this
 * @var string $appName
 * @var list<array{label: string, path: string}> $menu
 */
$this->title('Accueil');
?>
<h1 class="h3 mb-4"><?= $this->e($appName) ?></h1>
<?php if ($menu === []): ?>
    <p class="text-body-secondary">Aucune table n'a été générée.</p>
<?php else: ?>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3">
        <?php foreach ($menu as $item): ?>
            <div class="col">
                <a class="card h-100 text-decoration-none card-link-hover" href="<?= $this->e($this->url($item['path'])) ?>">
                    <div class="card-body">
                        <h2 class="h5 card-title mb-0"><?= $this->e($item['label']) ?></h2>
                    </div>
                </a>
            </div>
        <?php endforeach ?>
    </div>
<?php endif ?>
