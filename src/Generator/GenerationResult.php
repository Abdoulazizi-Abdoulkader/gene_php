<?php

declare(strict_types=1);

namespace Gene\Generator;

final readonly class GenerationResult
{
    /**
     * @param array<string, string> $files    chemin relatif => contenu
     * @param list<string>          $warnings
     */
    public function __construct(
        public array $files,
        public array $warnings,
        public int $entityCount,
    ) {}
}
