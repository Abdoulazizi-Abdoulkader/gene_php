<?php

declare(strict_types=1);

namespace Gene\Command;

use Gene\Config\ProfileStore;
use Gene\Generator\GenerationOptions;
use Gene\Generator\Project;
use Gene\Naming\Inflector;
use Gene\Schema\ConnectionError;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'generate', description: 'Génère une application CRUD à partir d\'une base MySQL/MariaDB')]
final class GenerateCommand extends Command
{
    use DatabaseOptions;

    public function __construct(
        private readonly string $projectDir,
        private readonly ProfileStore $profiles = new ProfileStore(),
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->configureDatabaseOptions($this);
        $this
            ->addOption('app', 'a', InputOption::VALUE_REQUIRED, 'Nom de l\'application (défaut : dérivé du nom de la base)')
            ->addOption('out', 'o', InputOption::VALUE_REQUIRED, 'Dossier de sortie (défaut : output/<app>)')
            ->addOption('tables', 't', InputOption::VALUE_REQUIRED, 'Tables à générer, séparées par des virgules (défaut : toutes)')
            ->addOption('exclude', 'x', InputOption::VALUE_REQUIRED, 'Tables à exclure, séparées par des virgules')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Afficher ce qui serait écrit, sans rien écrire')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Écraser aussi les fichiers modifiés à la main')
            ->setHelp(<<<'HELP'
                Exemples :
                  <info>GENE_DB_PASSWORD=secret bin/gene generate -d boutique -u dev</info>
                  <info>bin/gene generate --profile=boutique</info>
                  <info>bin/gene generate --dsn="mysql:host=db;dbname=boutique" -u dev --password-stdin < mdp.txt</info>
                  <info>bin/gene generate -d boutique -t client,commande --dry-run</info>

                Une nouvelle génération dans le même dossier met à jour les fichiers intacts et conserve ceux
                que vous avez modifiés (empreintes dans .gene.json). --force écrase tout.
                HELP);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $profile = $this->profile($input, $this->profiles);
        $settings = $this->settings($input, $io, $profile);

        try {
            $schema = $settings->readSchema();
        } catch (\PDOException $e) {
            $io->error(ConnectionError::explain($e));

            return Command::FAILURE;
        }

        $tables = self::csv($input->getOption('tables'));
        $schema = $schema->filter($tables !== [] ? $tables : ($profile !== null ? $profile->tables : []), self::csv($input->getOption('exclude')));

        $app = $input->getOption('app');
        $appName = is_string($app) && $app !== '' ? $app : ($profile !== null && $profile->app !== '' ? $profile->app : Inflector::className($schema->database));
        $options = new GenerationOptions($appName, $settings->dsn(), $settings->user);

        $out = $input->getOption('out');
        $outputDir = is_string($out) && $out !== '' ? $out : ($profile !== null && $profile->out !== '' ? $profile->out : Project::defaultOutputDir($this->projectDir, $appName));

        try {
            $outcome = (new Project())->generate($schema, $options, $outputDir, (bool) $input->getOption('dry-run'), (bool) $input->getOption('force'));
        } catch (\RuntimeException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        Presenter::outcome($io, $outcome, $appName, $output->isVerbose());
        if (!$outcome->dryRun) {
            Presenter::nextSteps($io, $outputDir, false);
        }

        return Command::SUCCESS;
    }
}
