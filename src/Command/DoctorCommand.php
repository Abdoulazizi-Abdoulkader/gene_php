<?php

declare(strict_types=1);

namespace Gene\Command;

use Gene\Config\ProfileStore;
use Gene\Schema\ConnectionError;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Vérifie que la machine peut faire tourner gene et les applications générées,
 * et indique la correction adaptée au système (Linux ou Windows).
 */
#[AsCommand(name: 'doctor', description: 'Vérifie l\'installation (PHP, extensions, dossiers) et, en option, la connexion')]
final class DoctorCommand extends Command
{
    use DatabaseOptions;

    /** Extensions : nom => [obligatoire, rôle, paquet Debian/Ubuntu]. */
    private const EXTENSIONS = [
        'pdo_mysql' => [true, 'connexion à MySQL/MariaDB', 'php-mysql'],
        'mbstring' => [true, 'textes accentués (validation, libellés)', 'php-mbstring'],
        'intl' => [false, 'translittération des noms accentués en identifiants', 'php-intl'],
        'zip' => [false, 'téléchargement en zip depuis l\'interface web', 'php-zip'],
    ];

    public function __construct(
        private readonly string $projectDir,
        private readonly ProfileStore $profiles = new ProfileStore(),
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->configureDatabaseOptions($this);
        $this->addOption('connect', 'c', InputOption::VALUE_NONE, 'Tester aussi la connexion (avec --host, --user… ou --profile)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $windows = PHP_OS_FAMILY === 'Windows';
        $rows = [];
        $failures = 0;
        $add = static function (?bool $ok, string $item, string $detail) use (&$rows, &$failures): void {
            $rows[] = [$ok === true ? '<info>✔</info>' : ($ok === false ? '<error>✖</error>' : '<comment>!</comment>'), $item, $detail];
            if ($ok === false) {
                ++$failures;
            }
        };

        $io->title('gene doctor — ' . PHP_OS_FAMILY . ', PHP ' . PHP_VERSION);

        $add(version_compare(PHP_VERSION, '8.3.0', '>='), 'PHP ≥ 8.3', PHP_VERSION . ' (' . PHP_BINARY . ')');
        $ini = php_ini_loaded_file();
        $add($ini !== false ? true : null, 'php.ini', $ini !== false ? $ini : 'aucun fichier chargé' . ($windows ? ' : copiez php.ini-development en php.ini à côté de php.exe' : ''));

        foreach (self::EXTENSIONS as $extension => [$required, $role, $package]) {
            $loaded = extension_loaded($extension);
            $hint = $windows
                ? sprintf('activez « extension=%s » dans php.ini', $extension)
                : sprintf('sudo apt install %s (Debian/Ubuntu) ou l\'équivalent dnf', $package);
            $add($loaded ? true : ($required ? false : null), 'extension ' . $extension, $loaded ? $role : $role . ' — ' . $hint);
        }

        $add(is_file($this->projectDir . '/vendor/autoload.php'), 'dépendances Composer', $this->projectDir . DIRECTORY_SEPARATOR . 'vendor');

        $outputPath = $this->projectDir . DIRECTORY_SEPARATOR . 'output';
        $writable = is_dir($outputPath) ? is_writable($outputPath) : is_writable($this->projectDir);
        $add($writable, 'dossier de sortie par défaut', $outputPath . ($writable ? '' : ' — non accessible en écriture'));

        $configDir = dirname($this->profiles->path());
        $add(is_dir($configDir) ? is_writable($configDir) : null, 'profils', $this->profiles->path() . (is_dir($configDir) ? '' : ' (créé au premier enregistrement)'));

        if ($input->getOption('connect') || $input->getOption('profile') !== null) {
            try {
                $pdo = $this->settings($input, $io, $this->profile($input, $this->profiles), false)->connect();
                $add(true, 'connexion', 'serveur ' . (string) $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION));
            } catch (\PDOException|\InvalidArgumentException $e) {
                $add(false, 'connexion', ConnectionError::explain($e));
            }
        }

        $io->table(['', 'Élément', 'Détail'], $rows);
        if ($failures > 0) {
            $io->error(sprintf('%d problème(s) bloquant(s). Voir docs/GUIDE.md, section « Dépannage ».', $failures));

            return Command::FAILURE;
        }
        $io->success('Tout est prêt. Lancez « gene » pour démarrer l\'assistant.');

        return Command::SUCCESS;
    }
}
