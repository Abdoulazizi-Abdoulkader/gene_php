<?php

declare(strict_types=1);

namespace Gene\Tests\Unit\Framework;

use App\Framework\Config;
use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
    public function testParseEnv(): void
    {
        $env = Config::parseEnv(<<<'ENV'
            # commentaire
            APP_NAME="Mon \"app\""
            export DB_DSN='mysql:host=x;dbname=y'
            APP_DEBUG=1 # en ligne
            DB_PASSWORD="p#ss=word"
            VIDE=
            ligne invalide
            ENV);

        self::assertSame([
            'APP_NAME' => 'Mon "app"',
            'DB_DSN' => 'mysql:host=x;dbname=y',
            'APP_DEBUG' => '1',
            'DB_PASSWORD' => 'p#ss=word',
            'VIDE' => '',
        ], $env);
    }

    public function testDotNotation(): void
    {
        $config = new Config(['db' => ['dsn' => 'x'], 'app' => ['debug' => false]]);

        self::assertSame('x', $config->get('db.dsn'));
        self::assertFalse($config->get('app.debug', true));
        self::assertSame('défaut', $config->get('db.absent', 'défaut'));
    }
}
