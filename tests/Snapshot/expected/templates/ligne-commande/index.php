<?php
/**
 * Liste « ligne_commande ». Généré par gene.
 *
 * @var App\Framework\View $this
 * @var App\Framework\Page<App\Entity\LigneCommande> $page
 * @var string $path
 */
$this->title('Ligne commande');
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <h1 class="h3 mb-0">Ligne commande <span class="badge text-bg-secondary align-middle fs-6"><?= $page->total ?></span></h1>
</div>

<?php if ($page->isEmpty()): ?>
    <div class="alert alert-light border"><?= $page->search !== '' ? 'Aucun résultat pour « ' . $this->e($page->search) . ' ».' : 'Aucun enregistrement pour le moment.' ?></div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
            <tr>
                <th scope="col"><?= $this->sortLink($path, 'commande_id', 'Commande', $page) ?></th>
                <th scope="col"><?= $this->sortLink($path, 'numero', 'Numero', $page) ?></th>
                <th scope="col"><?= $this->sortLink($path, 'quantite', 'Quantite', $page) ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($page->items as ['entity' => $item, 'labels' => $labels]): ?>
                <tr>
                    <td class="cell-truncate"><?= $this->display($labels['commande_id'] ?? $item->commandeId, 'string', 60) ?></td>
                    <td class="cell-truncate"><?= $this->display($item->numero, 'int', 60) ?></td>
                    <td class="cell-truncate"><?= $this->display($item->quantite, 'int', 60) ?></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
    <?= $this->partial('_pagination', ['page' => $page, 'path' => $path]) ?>
<?php endif ?>
