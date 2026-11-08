<?php
/**
 * Pagination d'une liste.
 *
 * @var App\Framework\View $this
 * @var App\Framework\Page<App\Framework\Entity> $page
 * @var string $path
 */
if ($page->lastPage() <= 1) {
    return;
}
$link = fn (int $p): string => $this->e($this->url($path, ['page' => $p, 'sort' => $page->sort, 'dir' => $page->direction, 'q' => $page->search]));
?>
<nav aria-label="Pagination">
    <ul class="pagination justify-content-center">
        <li class="page-item<?= $page->page <= 1 ? ' disabled' : '' ?>">
            <a class="page-link" href="<?= $link(max(1, $page->page - 1)) ?>" aria-label="Précédente">‹</a>
        </li>
        <?php foreach ($page->window() as $p): ?>
            <?php if ($p === 0): ?>
                <li class="page-item disabled"><span class="page-link">…</span></li>
            <?php else: ?>
                <li class="page-item<?= $p === $page->page ? ' active' : '' ?>">
                    <a class="page-link" href="<?= $link($p) ?>"<?= $p === $page->page ? ' aria-current="page"' : '' ?>><?= $p ?></a>
                </li>
            <?php endif ?>
        <?php endforeach ?>
        <li class="page-item<?= $page->page >= $page->lastPage() ? ' disabled' : '' ?>">
            <a class="page-link" href="<?= $link(min($page->lastPage(), $page->page + 1)) ?>" aria-label="Suivante">›</a>
        </li>
    </ul>
</nav>
