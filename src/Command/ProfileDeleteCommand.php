<?php

declare(strict_types=1);

namespace Gene\Command;

use Gene\Config\ProfileStore;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'profile:delete', description: 'Supprime un profil enregistré')]
final class ProfileDeleteCommand extends Command
{
    public function __construct(private readonly ProfileStore $profiles = new ProfileStore())
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Nom du profil');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $name = (string) $input->getArgument('name');
        if (!$this->profiles->delete($name)) {
            $io->error(sprintf('Profil « %s » inconnu.', $name));

            return Command::FAILURE;
        }
        $io->success(sprintf('Profil « %s » supprimé.', $name));

        return Command::SUCCESS;
    }
}
