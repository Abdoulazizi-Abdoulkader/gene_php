<?php

declare(strict_types=1);

namespace Gene\Tests\Unit;

use Gene\Schema\ConnectionError;
use Gene\Schema\ConnectionSettings;
use Gene\Schema\MySqlSchemaReader;
use PHPUnit\Framework\TestCase;

final class ConnectionTest extends TestCase
{
    public function testDsn(): void
    {
        self::assertSame('mysql:host=db;port=3307;dbname=boutique;charset=utf8mb4', (new ConnectionSettings('db', '3307', 'boutique'))->dsn());
        self::assertSame('mysql:host=db;port=3306;charset=utf8mb4', MySqlSchemaReader::dsn('db', '3306'), 'sans base : serveur seul');
        self::assertSame('mysql:host=x;dbname=y', (new ConnectionSettings(rawDsn: 'mysql:host=x;dbname=y'))->dsn());
        self::assertSame('autre', (new ConnectionSettings('db', '3306', 'a', 'u', 'p'))->withDatabase('autre')->database);
    }

    public function testDsnInjectionIsRefused(): void
    {
        foreach ([['db;unix_socket=/x', '3306', 'b'], ['db', '33a', 'b'], ['db', '3306', 'b;x=y'], ['', '3306', 'b']] as [$host, $port, $db]) {
            try {
                MySqlSchemaReader::dsn($host, $port, $db);
                self::fail("accepté : $host $port $db");
            } catch (\InvalidArgumentException) {
                $this->addToAssertionCount(1);
            }
        }
    }

    public function testReadSchemaRequiresDatabase(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new ConnectionSettings())->readSchema();
    }

    public function testErrorExplanations(): void
    {
        self::assertStringContainsString('pdo_mysql', ConnectionError::explain(new \PDOException('could not find driver')));
        self::assertStringContainsString('Accès refusé', ConnectionError::explain(new \PDOException('SQLSTATE[HY000] [1045] Access denied for user')));
        self::assertStringContainsString('injoignable', ConnectionError::explain(new \PDOException('SQLSTATE[HY000] [2002] Connection refused')));
        self::assertSame('autre chose', ConnectionError::explain(new \RuntimeException('autre chose')));
    }
}
