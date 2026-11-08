<?php

declare(strict_types=1);

namespace App\Framework;

/**
 * Rendu des gabarits PHP de templates/. Dans un gabarit, $this est la vue :
 * $this->e() échappe, $this->url() construit un lien, $this->csrfField() protège un formulaire.
 */
final class View
{
    /** @var array<string, mixed> */
    private array $shared = [];

    private ?string $title = null;

    public function __construct(
        private readonly string $templateDir,
        private readonly Request $request,
        private readonly Csrf $csrf,
        private readonly Session $session,
    ) {}

    /** Titre de la page, défini par le gabarit de contenu et lu par la mise en page. */
    public function title(string $title): void
    {
        $this->title = $title;
    }

    public function pageTitle(): ?string
    {
        return $this->title;
    }

    public function share(string $key, mixed $value): void
    {
        $this->shared[$key] = $value;
    }

    /**
     * Rend un gabarit puis l'insère dans la mise en page (variable $content).
     *
     * @param array<string, mixed> $data
     */
    public function render(string $template, array $data = [], ?string $layout = 'layout'): string
    {
        $content = $this->partial($template, $data);
        if ($layout === null) {
            return $content;
        }

        return $this->partial($layout, [...$data, 'content' => $content]);
    }

    /** @param array<string, mixed> $data */
    public function partial(string $template, array $data = []): string
    {
        if (preg_match('#^[A-Za-z0-9_\-/]+$#', $template) !== 1 || str_contains($template, '..')) {
            throw new \InvalidArgumentException(sprintf('Nom de gabarit invalide : « %s ».', $template));
        }
        $file = $this->templateDir . '/' . $template . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException(sprintf('Gabarit introuvable : %s', $file));
        }

        return (function (string $__file, array $__data): string {
            extract($__data, EXTR_SKIP);
            ob_start();
            try {
                require $__file;

                return (string) ob_get_contents();
            } finally {
                ob_end_clean();
            }
        })($file, [...$this->shared, ...$data]);
    }

    /** Échappe pour HTML (contenu et attributs). À utiliser pour toute donnée affichée. */
    public function e(mixed $value): string
    {
        if ($value === null) {
            return '';
        }
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return htmlspecialchars(is_scalar($value) || $value instanceof \Stringable ? (string) $value : '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /** @param array<string, scalar|null> $query */
    public function url(string $path = '/', array $query = []): string
    {
        $url = $this->request->basePath . '/' . ltrim($path, '/');
        $query = array_filter($query, static fn($v): bool => $v !== null && $v !== '');

        return $query === [] ? $url : $url . '?' . http_build_query($query);
    }

    public function asset(string $path): string
    {
        return $this->url('assets/' . ltrim($path, '/'));
    }

    /** Le chemin courant commence-t-il par $path ? (lien actif du menu) */
    public function isActive(string $path): bool
    {
        $path = '/' . trim($path, '/');

        return $path === '/' ? $this->request->path === '/' : ($this->request->path === $path || str_starts_with($this->request->path, $path . '/'));
    }

    public function csrfField(): string
    {
        return '<input type="hidden" name="_token" value="' . $this->e($this->csrf->token()) . '">';
    }

    /** @return list<array{type: string, message: string}> */
    public function flashes(): array
    {
        return $this->session->consumeFlashes();
    }

    /**
     * Valeur formatée pour l'affichage (déjà échappée).
     */
    public function display(mixed $value, string $type, int $truncate = 0): string
    {
        if ($value === null || $value === '') {
            return '<span class="text-body-tertiary">—</span>';
        }
        $text = match ($type) {
            'bool' => $value ? 'Oui' : 'Non',
            'date' => self::formatDate((string) $value, 'Y-m-d', 'd/m/Y'),
            'datetime' => self::formatDate(substr((string) $value, 0, 19), 'Y-m-d H:i:s', 'd/m/Y H:i'),
            default => is_scalar($value) ? (string) $value : '',
        };
        if ($truncate > 0 && mb_strlen($text) > $truncate) {
            $text = mb_substr($text, 0, $truncate - 1) . '…';
        }

        return $this->e($text);
    }

    /**
     * En-tête de colonne cliquable pour trier une liste (la recherche en cours est conservée).
     *
     * @param Page<Entity> $page
     */
    public function sortLink(string $path, string $column, string $label, Page $page): string
    {
        $active = $column === $page->sort;
        $direction = $active && $page->direction === 'asc' ? 'desc' : 'asc';
        $arrow = $active ? ($page->direction === 'asc' ? ' ▲' : ' ▼') : '';

        return sprintf(
            '<a class="link-body-emphasis text-decoration-none" href="%s"%s>%s%s</a>',
            $this->e($this->url($path, ['sort' => $column, 'dir' => $direction, 'q' => $page->search])),
            $active ? ' aria-sort="' . ($page->direction === 'asc' ? 'ascending' : 'descending') . '"' : '',
            $this->e($label),
            $arrow,
        );
    }

    private static function formatDate(string $value, string $from, string $to): string
    {
        $date = \DateTimeImmutable::createFromFormat('!' . $from, $value);

        return $date !== false ? $date->format($to) : $value;
    }
}
