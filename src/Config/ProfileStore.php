<?php

declare(strict_types=1);

namespace Gene\Config;

/**
 * Profils enregistrés dans le dossier de configuration de l'utilisateur :
 * GENE_CONFIG_DIR, sinon %APPDATA%\gene (Windows), $XDG_CONFIG_HOME/gene ou ~/.config/gene (Linux, macOS).
 */
final class ProfileStore
{
    private const FILE = 'profiles.json';

    private readonly string $dir;

    public function __construct(?string $dir = null)
    {
        $this->dir = rtrim($dir ?? self::defaultDir(), '/\\');
    }

    public static function defaultDir(): string
    {
        $custom = getenv('GENE_CONFIG_DIR');
        if (is_string($custom) && $custom !== '') {
            return $custom;
        }
        if (PHP_OS_FAMILY === 'Windows') {
            $appData = getenv('APPDATA');
            if (is_string($appData) && $appData !== '') {
                return $appData . '\\gene';
            }
        }
        $xdg = getenv('XDG_CONFIG_HOME');
        if (is_string($xdg) && $xdg !== '') {
            return $xdg . '/gene';
        }
        $home = getenv('HOME') ?: getenv('USERPROFILE') ?: sys_get_temp_dir();

        return $home . '/.config/gene';
    }

    public static function isValidName(string $name): bool
    {
        return preg_match('/^[A-Za-z0-9][A-Za-z0-9_.-]{0,63}$/', $name) === 1;
    }

    public function path(): string
    {
        return $this->dir . DIRECTORY_SEPARATOR . self::FILE;
    }

    /** @return array<string, Profile> par nom, triés */
    public function all(): array
    {
        if (!is_file($this->path())) {
            return [];
        }
        $data = json_decode((string) file_get_contents($this->path()), true);
        $profiles = [];
        foreach (is_array($data) && is_array($data['profiles'] ?? null) ? $data['profiles'] : [] as $name => $profile) {
            if (is_string($name) && is_array($profile)) {
                $profiles[$name] = Profile::fromArray($profile);
            }
        }
        ksort($profiles, SORT_NATURAL | SORT_FLAG_CASE);

        return $profiles;
    }

    public function get(string $name): ?Profile
    {
        return $this->all()[$name] ?? null;
    }

    public function save(string $name, Profile $profile): void
    {
        if (!self::isValidName($name)) {
            throw new \InvalidArgumentException('Nom de profil invalide : lettres, chiffres, « _ », « - » et « . » (64 caractères au plus).');
        }
        $profiles = $this->all();
        $profiles[$name] = $profile;
        $this->persist($profiles);
    }

    public function delete(string $name): bool
    {
        $profiles = $this->all();
        if (!isset($profiles[$name])) {
            return false;
        }
        unset($profiles[$name]);
        $this->persist($profiles);

        return true;
    }

    /** @param array<string, Profile> $profiles */
    private function persist(array $profiles): void
    {
        if (!is_dir($this->dir) && !mkdir($this->dir, 0o700, true) && !is_dir($this->dir)) {
            throw new \RuntimeException(sprintf('Impossible de créer le dossier %s.', $this->dir));
        }
        ksort($profiles, SORT_NATURAL | SORT_FLAG_CASE);
        $json = json_encode(
            ['version' => 1, 'profiles' => array_map(static fn(Profile $p): array => $p->toArray(), $profiles)],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
        );
        if (file_put_contents($this->path(), $json . "\n") === false) {
            throw new \RuntimeException(sprintf('Impossible d\'écrire %s.', $this->path()));
        }
        if (PHP_OS_FAMILY !== 'Windows') {
            @chmod($this->path(), 0o600); // lisible par son seul propriétaire (noms d'hôte, comptes)
        }
    }
}
