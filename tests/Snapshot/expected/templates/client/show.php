<?php
/**
 * Fiche « client ». Généré par gene.
 *
 * @var App\Framework\View $this
 * @var App\Entity\Client $item
 * @var array<string, string|null> $labels libellés des clés étrangères
 * @var string $path
 */
$itemPath = $path . '/' . rawurlencode((string) $item->id);
$this->title('Client' . ' : ' . (string) ($item->nom ?? $item->id));
?>
<nav aria-label="Fil d'Ariane">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= $this->e($this->url($path)) ?>">Client</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= $this->e($item->nom ?? $item->id) ?></li>
    </ol>
</nav>

<div class="card">
    <div class="card-body">
        <dl class="row mb-0 detail-list">
            <dt class="col-sm-4 col-lg-3">Id</dt>
            <dd class="col-sm-8 col-lg-9"><?= $this->display($item->id, 'int') ?></dd>
            <dt class="col-sm-4 col-lg-3">Nom</dt>
            <dd class="col-sm-8 col-lg-9"><?= $this->display($item->nom, 'string') ?></dd>
            <dt class="col-sm-4 col-lg-3">Email</dt>
            <dd class="col-sm-8 col-lg-9"><?= $this->display($item->email, 'string') ?></dd>
            <dt class="col-sm-4 col-lg-3">Date naissance</dt>
            <dd class="col-sm-8 col-lg-9"><?= $this->display($item->dateNaissance, 'date') ?></dd>
            <dt class="col-sm-4 col-lg-3">Actif</dt>
            <dd class="col-sm-8 col-lg-9"><?= $this->display($item->actif, 'bool') ?></dd>
            <dt class="col-sm-4 col-lg-3">Solde</dt>
            <dd class="col-sm-8 col-lg-9"><?= $this->display($item->solde, 'decimal') ?></dd>
            <dt class="col-sm-4 col-lg-3">Statut</dt>
            <dd class="col-sm-8 col-lg-9"><?= $this->display($item->statut, 'enum') ?></dd>
            <dt class="col-sm-4 col-lg-3">Pays</dt>
            <dd class="col-sm-8 col-lg-9">
                <?php if ($item->paysCode !== null): ?>
                    <a href="<?= $this->e($this->url('/pays/' . rawurlencode((string) $item->paysCode))) ?>"><?= $this->e($labels['pays_code'] ?? $item->paysCode) ?></a>
                <?php else: ?>
                    <?= $this->display(null, 'string') ?>
                <?php endif ?>
            </dd>
            <dt class="col-sm-4 col-lg-3">Parrain</dt>
            <dd class="col-sm-8 col-lg-9">
                <?php if ($item->parrainId !== null): ?>
                    <a href="<?= $this->e($this->url('/client/' . rawurlencode((string) $item->parrainId))) ?>"><?= $this->e($labels['parrain_id'] ?? $item->parrainId) ?></a>
                <?php else: ?>
                    <?= $this->display(null, 'string') ?>
                <?php endif ?>
            </dd>
            <dt class="col-sm-4 col-lg-3">Notes</dt>
            <dd class="col-sm-8 col-lg-9 pre-wrap"><?= $this->display($item->notes, 'text') ?></dd>
            <dt class="col-sm-4 col-lg-3">Cree le</dt>
            <dd class="col-sm-8 col-lg-9"><?= $this->display($item->creeLe, 'datetime') ?></dd>
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
