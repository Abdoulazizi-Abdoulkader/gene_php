<?php

declare(strict_types=1);

namespace Gene\Generator;

final readonly class ProjectOutcome
{
    public function __construct(
        public GenerationResult $result,
        public WriteReport $report,
        public string $outputDir,
        public bool $dryRun,
    ) {}
}
