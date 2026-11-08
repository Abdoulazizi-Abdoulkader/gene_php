<?php

declare(strict_types=1);

namespace Gene\Command;

use Gene\Config\ProfileStore;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'profile:list', description: 'Liste les profils enregistrés')]
final class ProfileListCommand extends Command
{
    public function __construct(private readonly ProfileStore $profiles = new ProfileStore())
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $profiles = $this->profiles->all();
        if ($profiles === []) {
            $io->text('Aucun profil. L\'assistant (« gene ») propose d\'en créer un à la fin d\'une génération.');

            return Command::SUCCESS;
        }
        $rows = [];
        foreach ($profiles as $name => $p) {
            $rows[] = [$name, sprintf('%s@%s:%s', $p->user, $p->host, $p->port), $p->database, $p->app, $p->tables === [] ? 'toutes' : implode(', ', $p->tables), $p->out];
        }
        $io->table(['Profil', 'Serveur', 'Base', 'Application', 'Tables', 'Dossier'], $rows);
        $io->text('Fichier : ' . $this->profiles->path());

        return Command::SUCCESS;
    }
}
