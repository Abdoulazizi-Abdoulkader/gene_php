<?php

declare(strict_types=1);

namespace Gene\Command;

use Gene\Generator\ProjectOutcome;
use Gene\Generator\WriteAction;
use Symfony\Component\Console\Style\SymfonyStyle;

/** Affichage commun du résultat d'une génération (commande generate et assistant). */
final class Presenter
{
    public static function outcome(SymfonyStyle $io, ProjectOutcome $outcome, string $appName, bool $detailed): void
    {
        foreach ($outcome->result->warnings as $warning) {
            $io->warning($warning);
        }

        $report = $outcome->report;
        if ($detailed || $outcome->dryRun) {
            $io->table(['Fichier', 'Action'], array_map(
                static fn(string $path, WriteAction $action): array => [$path, $action->value],
                array_keys($report->all()),
                $report->all(),
            ));
        }
        $io->definitionList(...array_map(static fn(string $k, int $v): array => [$k => (string) $v], array_keys($report->summary()), $report->summary()));

        $skipped = $report->paths(WriteAction::Skipped);
        if ($skipped !== []) {
            $io->note("Fichiers modifiés à la main, conservés (--force pour les écraser) :\n- " . implode("\n- ", $skipped));
        }

        if ($outcome->dryRun) {
            $io->success(sprintf('Simulation terminée : %d table(s), rien n\'a été écrit.', $outcome->result->entityCount));
        } else {
            $io->success(sprintf('%s généré dans %s (%d table(s)).', $appName, $outcome->outputDir, $outcome->result->entityCount));
        }
    }

    /** Commande de lancement, valable telle quelle sous Linux, macOS et Windows. */
    public static function serveCommand(string $outputDir, int $port = 8080): string
    {
        $public = rtrim($outputDir, '/\\') . DIRECTORY_SEPARATOR . 'public';

        return sprintf('php -S localhost:%d -t "%s"', $port, $public);
    }

    public static function nextSteps(SymfonyStyle $io, string $outputDir, bool $passwordWritten): void
    {
        $steps = ['Étapes suivantes :'];
        if (!$passwordWritten) {
            $steps[] = sprintf('  • Renseignez DB_PASSWORD dans <info>%s</info>', $outputDir . DIRECTORY_SEPARATOR . '.env');
        }
        $steps[] = '  • Lancez : <info>' . self::serveCommand($outputDir) . '</info>';
        $steps[] = '  • Ouvrez http://localhost:8080';
        $io->text($steps);
    }
}
