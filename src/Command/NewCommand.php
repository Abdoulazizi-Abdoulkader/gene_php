<?php

declare(strict_types=1);

namespace Gene\Command;

use Gene\Config\Profile;
use Gene\Config\ProfileStore;
use Gene\Generator\GenerationOptions;
use Gene\Generator\ModelBuilder;
use Gene\Generator\Project;
use Gene\Naming\Inflector;
use Gene\Schema\ConnectionError;
use Gene\Schema\ConnectionSettings;
use Gene\Schema\MySqlSchemaReader;
use Gene\Schema\Schema;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Assistant interactif : pose les questions une à une, vérifie chaque réponse, puis génère.
 * C'est la commande lancée par « gene » sans argument.
 */
#[AsCommand(name: 'new', description: 'Assistant pas à pas : connexion, base, tables, puis génération', aliases: ['assistant'])]
final class NewCommand extends Command
{
    private const NEW_CONNECTION = 'Nouvelle connexion';
    private const ALL_TABLES = 'toutes';
    private const MAX_ATTEMPTS = 3;

    public function __construct(
        private readonly string $projectDir,
        private readonly ProfileStore $profiles = new ProfileStore(),
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp(<<<'HELP'
            Lance un assistant qui demande, dans l'ordre : le serveur et le compte, la base, les tables,
            le nom de l'application et le dossier de sortie. La connexion est testée avant de continuer.

            À la fin, l'assistant propose d'enregistrer ces réglages comme profil (sans le mot de passe),
            puis de lancer l'application générée.

            Pour un usage sans questions (script, CI), utilisez <info>gene generate</info> avec ses options.
            HELP);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        if (!$input->isInteractive()) {
            $io->error('L\'assistant pose des questions : il ne fonctionne pas en mode non interactif. Utilisez « gene generate --database=… » (voir « gene generate --help »).');

            return Command::INVALID;
        }

        $io->title('gene — assistant de génération');
        $io->text('Répondez aux questions ; la valeur entre crochets est utilisée si vous appuyez sur Entrée. Ctrl+C pour quitter.');

        $profileName = $this->chooseProfile($io);
        $profile = $profileName !== null ? $this->profiles->get($profileName) : null;

        // 1. Serveur et compte, testés immédiatement.
        $io->section('1. Connexion au serveur MySQL/MariaDB');
        [$server, $pdo] = $this->connect($io, $profile) ?? [null, null];
        if ($server === null || $pdo === null) {
            return Command::FAILURE;
        }

        // 2. Base.
        $io->section('2. Base de données');
        $settings = $server->withDatabase($this->chooseDatabase($io, $pdo, $profile));
        try {
            $schema = $settings->readSchema();
        } catch (\PDOException $e) {
            $io->error(ConnectionError::explain($e));

            return Command::FAILURE;
        }

        // 3. Tables.
        $io->section(sprintf('3. Tables de « %s »', $schema->database));
        $allTables = array_keys($schema->tables);
        $schema = $this->chooseTables($io, $schema, $profile);

        // 4. Application et dossier.
        $io->section('4. Application à générer');
        $appName = (string) $io->askQuestion((new Question('Nom de l\'application', $profile !== null && $profile->app !== '' ? $profile->app : Inflector::className($schema->database)))
            ->setValidator(static function (mixed $value): string {
                $value = trim((string) $value);
                if (!GenerationOptions::isValidAppName($value)) {
                    throw new \InvalidArgumentException('Lettres, chiffres, « _ » et « - », en commençant par une lettre.');
                }

                return $value;
            }));
        $outputDir = self::absolute((string) $io->ask(
            'Dossier de sortie',
            $profile !== null && $profile->out !== '' ? $profile->out : Project::defaultOutputDir($this->projectDir, $appName),
        ));
        $force = false;
        if (Project::isGenerated($outputDir)) {
            $io->note('Ce dossier contient déjà une application générée : elle sera mise à jour. Les fichiers que vous avez modifiés sont conservés.');
            $force = self::yesNo($io, 'Écraser quand même les fichiers modifiés à la main ?', false);
        }

        // 5. Récapitulatif.
        $io->section('5. Récapitulatif');
        $io->definitionList(
            ['Serveur' => sprintf('%s@%s:%s', $settings->user, $settings->host, $settings->port)],
            ['Base' => $schema->database],
            ['Tables' => implode(', ', array_keys($schema->tables))],
            ['Application' => $appName],
            ['Dossier' => $outputDir . ($force ? ' (écrasement forcé)' : '')],
        );
        if (!self::yesNo($io, 'Générer maintenant ?', true)) {
            $io->text('Abandon : rien n\'a été écrit.');

            return Command::SUCCESS;
        }

        try {
            $outcome = (new Project())->generate($schema, new GenerationOptions($appName, $settings->dsn(), $settings->user), $outputDir, false, $force);
        } catch (\RuntimeException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }
        Presenter::outcome($io, $outcome, $appName, $output->isVerbose());

        // 6. Suites : profil, mot de passe, lancement.
        $this->offerProfile($io, $profileName, new Profile(
            $settings->host,
            $settings->port,
            $settings->user,
            $schema->database,
            $appName,
            $outputDir,
            array_keys($schema->tables) === $allTables ? [] : array_keys($schema->tables), // vide = toutes
        ));
        $configured = $settings->password === '' || self::envHasPassword($outputDir);
        if (!$configured && $this->offerPassword($io, $outputDir, $settings->password)) {
            $configured = true;
        }
        if (!$this->offerServer($io, $outputDir, $configured)) {
            Presenter::nextSteps($io, $outputDir, $configured);
        }

        return Command::SUCCESS;
    }

    private function chooseProfile(SymfonyStyle $io): ?string
    {
        $names = array_keys($this->profiles->all());
        if ($names === []) {
            return null;
        }
        $choice = (string) $io->askQuestion(new ChoiceQuestion('Partir d\'un profil enregistré ?', [self::NEW_CONNECTION, ...$names], 0));

        return $choice === self::NEW_CONNECTION ? null : $choice;
    }

    /** @return array{0: ConnectionSettings, 1: \PDO}|null */
    private function connect(SymfonyStyle $io, ?Profile $profile): ?array
    {
        $host = $profile !== null ? $profile->host : '127.0.0.1';
        $port = $profile !== null ? $profile->port : '3306';
        $user = $profile !== null ? $profile->user : ((string) getenv('GENE_DB_USER') ?: 'root');

        for ($attempt = 1; $attempt <= self::MAX_ATTEMPTS; ++$attempt) {
            $host = (string) $io->askQuestion((new Question('Hôte', $host))->setValidator(self::notEmpty(...)));
            $port = (string) $io->askQuestion((new Question('Port', $port))->setValidator(static function (mixed $v): string {
                $v = trim((string) $v);
                if (!ctype_digit($v) || (int) $v < 1 || (int) $v > 65535) {
                    throw new \InvalidArgumentException('Port numérique attendu (3306 par défaut ; souvent 3307 avec WAMP/MAMP).');
                }

                return $v;
            }));
            $user = (string) $io->askQuestion((new Question('Utilisateur', $user))->setValidator(self::notEmpty(...)));

            $envPassword = getenv('GENE_DB_PASSWORD');
            if (is_string($envPassword)) {
                $io->text('<comment>Mot de passe pris dans la variable GENE_DB_PASSWORD.</comment>');
                $password = $envPassword;
            } else {
                $password = self::askPassword($io);
            }

            try {
                $server = new ConnectionSettings($host, $port, '', $user, $password);
                $pdo = $server->connect();
                $io->text(sprintf('<info>✔ Connecté</info> à %s:%s (%s).', $host, $port, (string) $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION)));

                return [$server, $pdo];
            } catch (\PDOException|\InvalidArgumentException $e) {
                $io->error(ConnectionError::explain($e));
                if ($attempt < self::MAX_ATTEMPTS) {
                    $io->text(sprintf('Essai %d sur %d : corrigez les valeurs.', $attempt + 1, self::MAX_ATTEMPTS));
                }
                if (str_contains($e->getMessage(), 'could not find driver')) {
                    return null; // inutile de réessayer : c'est l'installation de PHP qu'il faut corriger
                }
            }
        }
        $io->text('Lancez « gene doctor » pour vérifier l\'installation.');

        return null;
    }

    private function chooseDatabase(SymfonyStyle $io, \PDO $pdo, ?Profile $profile): string
    {
        try {
            $databases = MySqlSchemaReader::listDatabases($pdo);
        } catch (\PDOException) {
            $databases = []; // compte sans droit SHOW DATABASES : saisie libre
        }
        $default = $profile !== null && $profile->database !== '' ? $profile->database : null;

        if ($databases === []) {
            return (string) $io->askQuestion((new Question('Nom de la base', $default))->setValidator(self::notEmpty(...)));
        }

        $question = new ChoiceQuestion('Base à utiliser (numéro ou nom)', $databases, $default !== null && in_array($default, $databases, true) ? $default : null);
        $question->setAutocompleterValues($databases)->setErrorMessage('« %s » n\'est pas dans la liste.');

        return (string) $io->askQuestion($question);
    }

    private function chooseTables(SymfonyStyle $io, Schema $schema, ?Profile $profile): Schema
    {
        $builder = new ModelBuilder();
        $builder->build($schema);
        $rows = [];
        foreach ($schema->tables as $table) {
            $mode = match (true) {
                !$table->hasPrimaryKey() => '<comment>ignorée (pas de clé primaire)</comment>',
                $table->singlePrimaryKey() === null => 'liste seule (clé composite)',
                default => 'CRUD complet',
            };
            $rows[] = [$table->name, (string) count($table->columns), (string) count($table->foreignKeys), $mode];
        }
        $io->table(['Table', 'Colonnes', 'Clés étrangères', 'Génération'], $rows);

        $tables = array_keys($schema->tables);
        if (count($tables) <= 1) {
            return $schema;
        }
        $choices = [self::ALL_TABLES, ...$tables];
        // La valeur par défaut d'un choix multiple s'exprime en indices (« 0 » = toutes).
        $remembered = $profile !== null ? array_keys(array_intersect($choices, $profile->tables)) : [];
        $question = new ChoiceQuestion(
            'Tables à générer (« toutes », ou numéros / noms séparés par des virgules)',
            $choices,
            $remembered !== [] ? implode(',', $remembered) : '0',
        );
        $question->setMultiselect(true)->setAutocompleterValues([self::ALL_TABLES, ...$tables]);

        /** @var list<string> $selected */
        $selected = (array) $io->askQuestion($question);
        if (in_array(self::ALL_TABLES, $selected, true)) {
            return $schema;
        }

        return $schema->filter($selected);
    }

    private function offerProfile(SymfonyStyle $io, ?string $current, Profile $profile): void
    {
        $question = $current !== null ? sprintf('Mettre à jour le profil « %s » avec ces réglages ?', $current) : 'Enregistrer ces réglages comme profil (sans le mot de passe) ?';
        if (!self::yesNo($io, $question, $current === null)) {
            return;
        }
        $name = $current ?? (string) $io->askQuestion((new Question('Nom du profil', $profile->database))->setValidator(static function (mixed $v): string {
            $v = trim((string) $v);
            if (!ProfileStore::isValidName($v)) {
                throw new \InvalidArgumentException('Lettres, chiffres, « _ », « - » et « . ».');
            }

            return $v;
        }));
        $this->profiles->save($name, $profile);
        $io->text(sprintf('<info>✔ Profil « %s » enregistré</info> (%s). Relance : <info>gene generate --profile=%s</info>', $name, $this->profiles->path(), $name));
    }

    private function offerPassword(SymfonyStyle $io, string $outputDir, string $password): bool
    {
        $env = $outputDir . DIRECTORY_SEPARATOR . '.env';
        if (!is_file($env) || !self::yesNo($io, 'Écrire le mot de passe dans le .env de l\'application ? (pratique en développement local ; jamais sur un serveur partagé)', false)) {
            return false;
        }
        $content = (string) file_get_contents($env);
        $updated = preg_replace('/^DB_PASSWORD=.*$/m', 'DB_PASSWORD="' . addcslashes($password, '"\\') . '"', $content, 1, $count);
        if ($count !== 1 || !is_string($updated) || file_put_contents($env, $updated) === false) {
            $io->warning('Ligne DB_PASSWORD introuvable dans .env : renseignez-la à la main.');

            return false;
        }
        $io->text('<info>✔ Mot de passe écrit</info> dans ' . $env);

        return true;
    }

    /** Le .env de l'application contient-il déjà un mot de passe (écrit lors d'un passage précédent) ? */
    private static function envHasPassword(string $outputDir): bool
    {
        $env = $outputDir . DIRECTORY_SEPARATOR . '.env';

        return is_file($env) && preg_match('/^DB_PASSWORD=(?!""\s*$)(?!\s*$).+/m', (string) file_get_contents($env)) === 1;
    }

    /** Lance le serveur intégré de PHP (bloquant jusqu'à Ctrl+C). Renvoie vrai si l'utilisateur l'a lancé. */
    private function offerServer(SymfonyStyle $io, string $outputDir, bool $ready): bool
    {
        if (!$ready) {
            return false; // sans mot de passe, l'application ne pourrait pas se connecter
        }
        if (!self::yesNo($io, 'Lancer l\'application maintenant ?', false)) {
            return false;
        }
        $port = self::freePort();
        $io->success(sprintf('Application disponible sur http://localhost:%d — Ctrl+C pour arrêter.', $port));
        passthru(sprintf('%s -S localhost:%d -t %s', escapeshellarg(PHP_BINARY), $port, escapeshellarg($outputDir . DIRECTORY_SEPARATOR . 'public')));

        return true;
    }

    private static function freePort(): int
    {
        for ($port = 8080; $port < 8100; ++$port) {
            $socket = @stream_socket_server('tcp://127.0.0.1:' . $port);
            if ($socket !== false) {
                fclose($socket);

                return $port;
            }
        }

        return 8080;
    }

    private static function notEmpty(mixed $value): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            throw new \InvalidArgumentException('Une valeur est nécessaire.');
        }

        return $value;
    }

    /** Chemin relatif → absolu (par rapport au dossier courant), pour un affichage sans ambiguïté. */
    private static function absolute(string $path): string
    {
        $path = trim($path);
        $isAbsolute = str_starts_with($path, '/') || str_starts_with($path, '\\') || preg_match('/^[A-Za-z]:[\\\\\/]/', $path) === 1;

        return $isAbsolute ? $path : (getcwd() ?: '.') . DIRECTORY_SEPARATOR . $path;
    }

    /** Question oui/non à la française : accepte o, oui, y, yes / n, non, no. */
    private static function yesNo(SymfonyStyle $io, string $question, bool $default): bool
    {
        $answer = $io->askQuestion((new Question($question . ' (o/n)', $default ? 'o' : 'n'))->setValidator(static function (mixed $v): bool {
            $v = strtolower(trim((string) $v));

            return match (true) {
                in_array($v, ['o', 'oui', 'y', 'yes'], true) => true,
                in_array($v, ['n', 'non', 'no'], true) => false,
                default => throw new \InvalidArgumentException('Répondez o (oui) ou n (non).'),
            };
        }));

        return $answer === true;
    }

    private static function askPassword(SymfonyStyle $io): string
    {
        $question = (new Question('Mot de passe (saisie masquée, vide si aucun)'))->setHidden(true)->setHiddenFallback(false);

        return (string) $io->askQuestion($question);
    }
}
