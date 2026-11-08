<?php

declare(strict_types=1);

namespace Gene\Generator;

final class WriteReport
{
    /** @var array<string, WriteAction> */
    private array $actions = [];

    public function add(string $path, WriteAction $action): void
    {
        $this->actions[$path] = $action;
    }

    /** @return array<string, WriteAction> */
    public function all(): array
    {
        return $this->actions;
    }

    /** @return array<string, int> nombre de fichiers par action */
    public function summary(): array
    {
        $summary = [];
        foreach ($this->actions as $action) {
            $summary[$action->value] = ($summary[$action->value] ?? 0) + 1;
        }

        return $summary;
    }

    /** @return list<string> */
    public function paths(WriteAction $action): array
    {
        return array_keys(array_filter($this->actions, static fn(WriteAction $a): bool => $a === $action));
    }
}
