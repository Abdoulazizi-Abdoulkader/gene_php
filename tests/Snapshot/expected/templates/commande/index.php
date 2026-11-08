<?php
/**
 * Liste « commande ». Généré par gene.
 *
 * @var App\Framework\View $this
 * @var App\Framework\Page<App\Entity\Commande> $page
 * @var string $path
 */
$this->title('Commande');
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <h1 class="h3 mb-0">Commande <span class="badge text-bg-secondary align-middle fs-6"><?= $page->total ?></span></h1>
    <a class="btn btn-primary" href="<?= $this->e($this->url($path . '/new')) ?>">Nouveau</a>
</div>

<?php if ($page->isEmpty()): ?>
    <div class="alert alert-light border"><?= $page->search !== '' ? 'Aucun résultat pour « ' . $this->e($page->search) . ' ».' : 'Aucun enregistrement pour le moment.' ?></div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
            <tr>
                <th scope="col"><?= $this->sortLink($path, 'id', 'Id', $page) ?></th>
                <th scope="col"><?= $this->sortLink($path, 'client_id', 'Client', $page) ?></th>
                <th scope="col"><?= $this->sortLink($path, 'passee_le', 'Passee le', $page) ?></th>
                <th scope="col"><?= $this->sortLink($path, 'heure_livraison', 'Heure livraison', $page) ?></th>
                <th scope="col"><?= $this->sortLink($path, 'montant_ttc', 'Montant ttc', $page) ?></th>
                <th scope="col" class="table-actions"><span class="visually-hidden">Actions</span></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($page->items as ['entity' => $item, 'labels' => $labels]): ?>
                <tr>
                    <td class="cell-truncate"><?= $this->display($item->id, 'int', 60) ?></td>
                    <td class="cell-truncate"><?= $this->display($labels['client_id'] ?? $item->clientId, 'string', 60) ?></td>
                    <td class="cell-truncate"><?= $this->display($item->passeeLe, 'datetime', 60) ?></td>
                    <td class="cell-truncate"><?= $this->display($item->heureLivraison, 'time', 60) ?></td>
                    <td class="cell-truncate"><?= $this->display($item->montantTtc, 'float', 60) ?></td>
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
