<?php

declare(strict_types=1);

namespace Gene\Command;

use Gene\Config\Profile;
use Gene\Config\ProfileStore;
use Gene\Schema\ConnectionSettings;
use Gene\Schema\Schema;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Options de connexion communes aux commandes, avec cet ordre de priorité :
 * option explicite > profil (--profile) > variable d'environnement > valeur par défaut.
 *
 * Le mot de passe ne passe jamais par un argument (il apparaîtrait dans l'historique du shell et la liste
 * des processus) : variable GENE_DB_PASSWORD, entrée standard (--password-stdin) ou saisie masquée.
 */
trait DatabaseOptions
{
    private function configureDatabaseOptions(Command $command): void
    {
        $command
            ->addOption('profile', 'p', InputOption::VALUE_REQUIRED, 'Profil enregistré à utiliser (voir « gene profile:list »)')
            ->addOption('dsn', null, InputOption::VALUE_REQUIRED, 'DSN PDO complet (ex. « mysql:host=127.0.0.1;dbname=boutique ») ; défaut : GENE_DSN')
            ->addOption('host', null, InputOption::VALUE_REQUIRED, 'Hôte MySQL/MariaDB (défaut : 127.0.0.1)')
            ->addOption('port', null, InputOption::VALUE_REQUIRED, 'Port (défaut : 3306)')
            ->addOption('database', 'd', InputOption::VALUE_REQUIRED, 'Base de données')
            ->addOption('user', 'u', InputOption::VALUE_REQUIRED, 'Utilisateur (défaut : GENE_DB_USER, sinon « root »)')
            ->addOption('password-stdin', null, InputOption::VALUE_NONE, 'Lire le mot de passe sur l\'entrée standard')
            ->addOption('no-password', null, InputOption::VALUE_NONE, 'Se connecter sans mot de passe, sans le demander');
    }

    private function profile(InputInterface $input, ProfileStore $store): ?Profile
    {
        $name = $input->getOption('profile');
        if (!is_string($name) || $name === '') {
            return null;
        }

        return $store->get($name) ?? throw new \InvalidArgumentException(sprintf('Profil « %s » inconnu. Profils : %s.', $name, implode(', ', array_keys($store->all())) ?: 'aucun'));
    }

    private function settings(InputInterface $input, SymfonyStyle $io, ?Profile $profile, bool $requireDatabase = true): ConnectionSettings
    {
        $option = static fn(string $name): ?string => is_string($input->getOption($name)) && $input->getOption($name) !== '' ? $input->getOption($name) : null;
        $env = static function (string $name): ?string {
            $value = getenv($name);

            return is_string($value) && $value !== '' ? $value : null;
        };

        $user = $option('user') ?? ($profile !== null ? $profile->user : null) ?? $env('GENE_DB_USER') ?? 'root';
        $password = $this->password($input, $io);

        $dsn = $option('dsn') ?? ($profile === null ? $env('GENE_DSN') : null);
        if ($dsn !== null) {
            return new ConnectionSettings(user: $user, password: $password, rawDsn: $dsn);
        }

        $database = $option('database') ?? $profile->database ?? '';
        if ($requireDatabase && $database === '') {
            throw new \InvalidArgumentException('Indiquez la base : --database=nom, --profile=…, --dsn=… ou la variable GENE_DSN. Sans option, « gene » lance l\'assistant.');
        }

        return new ConnectionSettings(
            $option('host') ?? $profile->host ?? '127.0.0.1',
            $option('port') ?? $profile->port ?? '3306',
            $database,
            $user,
            $password,
        );
    }

    private function password(InputInterface $input, SymfonyStyle $io): string
    {
        if ($input->getOption('password-stdin')) {
            return rtrim((string) stream_get_contents(STDIN), "\r\n");
        }
        $env = getenv('GENE_DB_PASSWORD');
        if (is_string($env)) {
            return $env;
        }
        if ($input->getOption('no-password') || !$input->isInteractive()) {
            return '';
        }

        return self::askPassword($io);
    }

    private static function askPassword(SymfonyStyle $io): string
    {
        $question = (new Question('Mot de passe de la base (saisie masquée, vide si aucun)'))->setHidden(true)->setHiddenFallback(false);

        return (string) $io->askQuestion($question);
    }

    /** @return list<string> */
    private static function csv(mixed $value): array
    {
        return Schema::parseTableList(is_string($value) ? $value : null);
    }
}
