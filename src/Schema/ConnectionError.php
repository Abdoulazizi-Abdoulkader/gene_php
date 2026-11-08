<?php

declare(strict_types=1);

namespace Gene\Schema;

/** Traduit les erreurs de connexion les plus fréquentes en explication et en piste de correction. */
final class ConnectionError
{
    public static function explain(\Throwable $e): string
    {
        $message = $e->getMessage();
        $windows = PHP_OS_FAMILY === 'Windows';

        return match (true) {
            str_contains($message, 'could not find driver') => 'L\'extension PHP pdo_mysql n\'est pas active. '
                . ($windows
                    ? 'Dans php.ini (' . (php_ini_loaded_file() ?: 'introuvable : copiez php.ini-development en php.ini') . '), retirez le « ; » devant « extension=pdo_mysql ».'
                    : 'Installez-la (Debian/Ubuntu : sudo apt install php-mysql ; Fedora : sudo dnf install php-mysqlnd).'),
            str_contains($message, '[1045]') => 'Accès refusé : utilisateur ou mot de passe incorrect, ou compte non autorisé depuis cet hôte.',
            str_contains($message, '[1049]') => 'Cette base n\'existe pas sur le serveur.',
            str_contains($message, '[1044]') => 'Le compte n\'a pas le droit d\'accéder à cette base.',
            str_contains($message, '[2002]'), str_contains($message, '[2003]') => 'Serveur injoignable : vérifiez l\'hôte, le port et que MySQL/MariaDB est démarré'
                . ($windows ? ' (Services Windows, XAMPP ou WAMP).' : ' (sudo systemctl status mysql).'),
            str_contains($message, '[2054]') => 'Méthode d\'authentification refusée par le serveur : mettez à jour PHP ou changez le plugin du compte.',
            default => $message,
        };
    }
}
