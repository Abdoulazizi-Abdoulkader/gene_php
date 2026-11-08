<?php
/**
 * Fiche « pays ». Généré par gene.
 *
 * @var App\Framework\View $this
 * @var App\Entity\Pays $item
 * @var array<string, string|null> $labels libellés des clés étrangères
 * @var string $path
 */
$itemPath = $path . '/' . rawurlencode((string) $item->code);
$this->title('Pays' . ' : ' . (string) ($item->libelle ?? $item->code));
?>
<nav aria-label="Fil d'Ariane">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= $this->e($this->url($path)) ?>">Pays</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= $this->e($item->libelle ?? $item->code) ?></li>
    </ol>
</nav>

<div class="card">
    <div class="card-body">
        <dl class="row mb-0 detail-list">
            <dt class="col-sm-4 col-lg-3">Code</dt>
            <dd class="col-sm-8 col-lg-9"><?= $this->display($item->code, 'string') ?></dd>
            <dt class="col-sm-4 col-lg-3">Libelle</dt>
            <dd class="col-sm-8 col-lg-9"><?= $this->display($item->libelle, 'string') ?></dd>
        </dl>
    </div>
</div>

<div class="d-flex flex-wrap gap-2 mt-3">
    <a class="btn btn-primary" href="<?= $this->e($this->url($itemPath . '/edit')) ?>">Modifier</a>
    <form method="post" action="<?= $this->e($this->url($itemPath . '/delete')) ?>" data-confirm="Supprimer définitivement cet enregistrement ?">
        <?= $this->csrfField() ?>
        <button type="submit" class="btn btn-outline-danger">Supprimer</button>
    </form>
    <a class="btn btn-link" href="<?= $this->e($this->url($path)) ?>">Retour à la liste</a>
</div>
