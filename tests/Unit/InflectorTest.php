<?php

declare(strict_types=1);

namespace Gene\Tests\Unit;

use Gene\Naming\Inflector;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class InflectorTest extends TestCase
{
    /** @return iterable<array{string, string}> */
    public static function classNames(): iterable
    {
        yield ['gestion_courrier', 'GestionCourrier'];
        yield ['client', 'Client'];
        yield ['LigneCommande', 'LigneCommande'];
        yield ['ligne-commande', 'LigneCommande'];
        yield ['élève', 'Eleve'];
        yield ['2fa_codes', 'T2faCodes'];
        yield ['list', 'ListItem'];
        yield ['class', 'ClassItem'];
        yield ['HTTPLog', 'HttpLog'];
    }

    #[DataProvider('classNames')]
    public function testClassName(string $table, string $expected): void
    {
        self::assertSame($expected, Inflector::className($table));
    }

    public function testClassNameNeverEmpty(): void
    {
        self::assertMatchesRegularExpression('/^[A-Z][A-Za-z0-9]+$/', Inflector::className('___'));
    }

    /** @return iterable<array{string, string}> */
    public static function properties(): iterable
    {
        yield ['date_naissance', 'dateNaissance'];
        yield ['ID', 'id'];
        yield ['pièce_jointe', 'pieceJointe'];
        yield ['1er_choix', 'c1erChoix'];
        yield ['this', 'thisValue'];
        yield ['dateNaissance', 'dateNaissance'];
    }

    #[DataProvider('properties')]
    public function testPropertyName(string $column, string $expected): void
    {
        self::assertSame($expected, Inflector::propertyName($column));
    }

    public function testSlug(): void
    {
        self::assertSame('gestion-courrier', Inflector::slug('Gestion_Courrier'));
        self::assertSame('eleve', Inflector::slug('Élève'));
    }

    /** @return iterable<array{string, string}> */
    public static function labels(): iterable
    {
        yield ['date_naissance', 'Date naissance'];
        yield ['id_client', 'Client'];
        yield ['client_id', 'Client'];
        yield ['id', 'Id'];
        yield ['prénom', 'Prénom'];
        yield ['dateNaissance', 'Date naissance'];
    }

    #[DataProvider('labels')]
    public function testLabel(string $column, string $expected): void
    {
        self::assertSame($expected, Inflector::label($column));
    }

    public function testTitleKeepsIdWords(): void
    {
        self::assertSame('Id card', Inflector::title('id_card'));
        self::assertSame('Ligne commande', Inflector::title('ligne_commande'));
    }
}
