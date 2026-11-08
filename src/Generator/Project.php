<?php

declare(strict_types=1);

namespace Gene\Generator;

use Gene\Schema\Schema;

/**
 * Le flux complet, partagé par la CLI, l'assistant et l'interface web : schéma → fichiers → disque.
 */
final class Project
{
    public function __construct(private readonly Generator $generator = new Generator()) {}

    /** Dossier de sortie par défaut : output/<App> dans le dossier de gene. */
    public static function defaultOutputDir(string $projectDir, string $appName): string
    {
        return rtrim($projectDir, '/\\') . DIRECTORY_SEPARATOR . 'output' . DIRECTORY_SEPARATOR . $appName;
    }

    /** Le dossier a-t-il déjà été généré par gene (régénération) ? */
    public static function isGenerated(string $outputDir): bool
    {
        return is_file($outputDir . '/' . FileWriter::MANIFEST);
    }

    public function generate(Schema $schema, GenerationOptions $options, string $outputDir, bool $dryRun = false, bool $force = false): ProjectOutcome
    {
        $result = $this->generator->generate($schema, $options);
        if ($result->entityCount === 0) {
            throw new \RuntimeException('Aucune table exploitable (une clé primaire est nécessaire) : rien à générer.');
        }
        $report = (new FileWriter($outputDir, $dryRun, $force))->write($result->files);

        return new ProjectOutcome($result, $report, $outputDir, $dryRun);
    }
}
