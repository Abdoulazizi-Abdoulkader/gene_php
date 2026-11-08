<?php

declare(strict_types=1);

namespace Gene\Generator;

final readonly class GenerationOptions
{
    /**
     * @param string $appName nom affiché de l'application
     * @param string $dsn     DSN écrit dans .env / .env.example (sans mot de passe)
     * @param string $dbUser  utilisateur écrit dans .env / .env.example
     */
    public function __construct(
        public string $appName,
        public string $dsn = 'mysql:host=127.0.0.1;port=3306;dbname=app;charset=utf8mb4',
        public string $dbUser = '',
    ) {
        if (!self::isValidAppName($appName)) {
            throw new \InvalidArgumentException('Nom d\'application invalide : lettres, chiffres, « _ » et « - », en commençant par une lettre (64 caractères au plus).');
        }
    }

    public static function isValidAppName(string $name): bool
    {
        return preg_match('/^[A-Za-z][A-Za-z0-9_-]{0,63}$/', $name) === 1;
    }

    /** Nom de paquet Composer : « Gestion_Courrier » → « app/gestion-courrier ». */
    public function packageName(): string
    {
        return 'app/' . strtolower(str_replace('_', '-', $this->appName));
    }
}
