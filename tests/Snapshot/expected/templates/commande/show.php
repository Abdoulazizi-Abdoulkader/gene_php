<?php
/**
 * Fiche « commande ». Généré par gene.
 *
 * @var App\Framework\View $this
 * @var App\Entity\Commande $item
 * @var array<string, string|null> $labels libellés des clés étrangères
 * @var string $path
 */
$itemPath = $path . '/' . rawurlencode((string) $item->id);
$this->title('Commande' . ' : ' . (string) ($item->id ?? $item->id));
?>
<nav aria-label="Fil d'Ariane">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= $this->e($this->url($path)) ?>">Commande</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= $this->e($item->id ?? $item->id) ?></li>
    </ol>
</nav>

<div class="card">
    <div class="card-body">
        <dl class="row mb-0 detail-list">
            <dt class="col-sm-4 col-lg-3">Id</dt>
            <dd class="col-sm-8 col-lg-9"><?= $this->display($item->id, 'int') ?></dd>
            <dt class="col-sm-4 col-lg-3">Client</dt>
            <dd class="col-sm-8 col-lg-9">
                <?php if ($item->clientId !== null): ?>
                    <a href="<?= $this->e($this->url('/client/' . rawurlencode((string) $item->clientId))) ?>"><?= $this->e($labels['client_id'] ?? $item->clientId) ?></a>
                <?php else: ?>
                    <?= $this->display(null, 'string') ?>
                <?php endif ?>
            </dd>
            <dt class="col-sm-4 col-lg-3">Passee le</dt>
            <dd class="col-sm-8 col-lg-9"><?= $this->display($item->passeeLe, 'datetime') ?></dd>
            <dt class="col-sm-4 col-lg-3">Heure livraison</dt>
            <dd class="col-sm-8 col-lg-9"><?= $this->display($item->heureLivraison, 'time') ?></dd>
            <dt class="col-sm-4 col-lg-3">Montant ttc</dt>
            <dd class="col-sm-8 col-lg-9"><?= $this->display($item->montantTtc, 'float') ?></dd>
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
