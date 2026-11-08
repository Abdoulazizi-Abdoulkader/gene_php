<?php

declare(strict_types=1);

namespace App\Framework;

/**
 * Une page de résultats. Chaque élément porte l'entité et les libellés de ses clés étrangères.
 *
 * @template T of Entity
 */
final readonly class Page
{
    /** @param list<array{entity: T, labels: array<string, string|null>}> $items */
    public function __construct(
        public array $items,
        public int $total,
        public int $page,
        public int $perPage,
        public string $sort,
        public string $direction,
        public string $search = '',
    ) {}

    public function lastPage(): int
    {
        return max(1, (int) ceil($this->total / max(1, $this->perPage)));
    }

    public function isEmpty(): bool
    {
        return $this->items === [];
    }

    /**
     * Numéros de page à afficher autour de la page courante (0 = ellipse).
     *
     * @return list<int>
     */
    public function window(int $around = 2): array
    {
        $last = $this->lastPage();
        $pages = [];
        for ($p = 1; $p <= $last; ++$p) {
            if ($p === 1 || $p === $last || abs($p - $this->page) <= $around) {
                $pages[] = $p;
            } elseif (end($pages) !== 0) {
                $pages[] = 0;
            }
        }

        return $pages;
    }
}
