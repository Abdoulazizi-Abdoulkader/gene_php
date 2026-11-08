<?php
/**
 * Liste « client ». Généré par gene.
 *
 * @var App\Framework\View $this
 * @var App\Framework\Page<App\Entity\Client> $page
 * @var string $path
 */
$this->title('Client');
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <h1 class="h3 mb-0">Client <span class="badge text-bg-secondary align-middle fs-6"><?= $page->total ?></span></h1>
    <a class="btn btn-primary" href="<?= $this->e($this->url($path . '/new')) ?>">Nouveau</a>
</div>

<form method="get" action="<?= $this->e($this->url($path)) ?>" class="d-flex gap-2 mb-3" role="search">
    <input type="search" class="form-control" name="q" value="<?= $this->e($page->search) ?>" placeholder="Rechercher" aria-label="Rechercher">
    <button type="submit" class="btn btn-outline-secondary">Rechercher</button>
    <?php if ($page->search !== ''): ?>
        <a class="btn btn-link" href="<?= $this->e($this->url($path)) ?>">Effacer</a>
    <?php endif ?>
</form>

<?php if ($page->isEmpty()): ?>
    <div class="alert alert-light border"><?= $page->search !== '' ? 'Aucun résultat pour « ' . $this->e($page->search) . ' ».' : 'Aucun enregistrement pour le moment.' ?></div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
            <tr>
                <th scope="col"><?= $this->sortLink($path, 'id', 'Id', $page) ?></th>
                <th scope="col"><?= $this->sortLink($path, 'nom', 'Nom', $page) ?></th>
                <th scope="col"><?= $this->sortLink($path, 'email', 'Email', $page) ?></th>
                <th scope="col"><?= $this->sortLink($path, 'date_naissance', 'Date naissance', $page) ?></th>
                <th scope="col"><?= $this->sortLink($path, 'actif', 'Actif', $page) ?></th>
                <th scope="col"><?= $this->sortLink($path, 'solde', 'Solde', $page) ?></th>
                <th scope="col"><?= $this->sortLink($path, 'statut', 'Statut', $page) ?></th>
                <th scope="col"><?= $this->sortLink($path, 'pays_code', 'Pays', $page) ?></th>
                <th scope="col" class="table-actions"><span class="visually-hidden">Actions</span></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($page->items as ['entity' => $item, 'labels' => $labels]): ?>
                <tr>
                    <td class="cell-truncate"><?= $this->display($item->id, 'int', 60) ?></td>
                    <td class="cell-truncate"><?= $this->display($item->nom, 'string', 60) ?></td>
                    <td class="cell-truncate"><?= $this->display($item->email, 'string', 60) ?></td>
                    <td class="cell-truncate"><?= $this->display($item->dateNaissance, 'date', 60) ?></td>
                    <td class="cell-truncate"><?= $this->display($item->actif, 'bool', 60) ?></td>
                    <td class="cell-truncate"><?= $this->display($item->solde, 'decimal', 60) ?></td>
                    <td class="cell-truncate"><?= $this->display($item->statut, 'enum', 60) ?></td>
                    <td class="cell-truncate"><?= $this->display($labels['pays_code'] ?? $item->paysCode, 'string', 60) ?></td>
                    <td class="table-actions text-end">
                        <?php $itemPath = $path . '/' . rawurlencode((string) $item->id) ?>
                        <a class="btn btn-sm btn-outline-secondary" href="<?= $this->e($this->url($itemPath)) ?>">Voir</a>
                        <a class="btn btn-sm btn-outline-primary" href="<?= $this->e($this->url($itemPath . '/edit')) ?>">Modifier</a>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
    <?= $this->partial('_pagination', ['page' => $page, 'path' => $path]) ?>
<?php endif ?>
