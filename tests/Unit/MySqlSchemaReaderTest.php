<?php

declare(strict_types=1);

namespace Gene\Tests\Unit;

use Gene\Schema\ColumnKind;
use Gene\Schema\MySqlSchemaReader;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class MySqlSchemaReaderTest extends TestCase
{
    /** @return iterable<array{string, string, ColumnKind}> */
    public static function types(): iterable
    {
        yield ['tinyint', 'tinyint(1)', ColumnKind::Bool];
        yield ['tinyint', 'tinyint(4)', ColumnKind::Int];
        yield ['tinyint', 'tinyint unsigned', ColumnKind::Int];
        yield ['bit', 'bit(1)', ColumnKind::Bool];
        yield ['bit', 'bit(8)', ColumnKind::Binary];
        yield ['bigint', 'bigint(20) unsigned', ColumnKind::Int];
        yield ['decimal', 'decimal(10,2)', ColumnKind::Decimal];
        yield ['double', 'double', ColumnKind::Float];
        yield ['varchar', 'varchar(255)', ColumnKind::String];
        yield ['longtext', 'longtext', ColumnKind::Text];
        yield ['enum', "enum('a','b')", ColumnKind::Enum];
        yield ['json', 'json', ColumnKind::Json];
        yield ['timestamp', 'timestamp', ColumnKind::DateTime];
        yield ['year', 'year(4)', ColumnKind::Year];
        yield ['mediumblob', 'mediumblob', ColumnKind::Binary];
        yield ['point', 'point', ColumnKind::Binary];
    }

    #[DataProvider('types')]
    public function testMapType(string $dataType, string $columnType, ColumnKind $expected): void
    {
        self::assertSame($expected, MySqlSchemaReader::mapType($dataType, $columnType));
    }

    public function testParseEnumHandlesQuotesAndCommas(): void
    {
        self::assertSame(['a', "l'été", 'x,y'], MySqlSchemaReader::parseEnum("enum('a','l''été','x,y')"));
    }

    /** @return iterable<string, array{mixed, string, ColumnKind, bool, array{string|null, bool}}> */
    public static function defaults(): iterable
    {
        yield 'mysql: aucun défaut' => [null, '', ColumnKind::String, false, [null, false]];
        yield 'mysql: littéral' => ['abc', '', ColumnKind::String, false, ['abc', false]];
        yield 'mysql: expression marquée' => ['CURRENT_TIMESTAMP', 'DEFAULT_GENERATED', ColumnKind::DateTime, false, [null, true]];
        yield 'mysql: CURRENT_TIMESTAMP ancien format' => ['CURRENT_TIMESTAMP', 'on update CURRENT_TIMESTAMP', ColumnKind::DateTime, false, [null, true]];
        yield 'mysql: uuid()' => ['uuid()', 'DEFAULT_GENERATED', ColumnKind::String, false, [null, true]];
        yield 'mariadb: NULL textuel' => ['NULL', '', ColumnKind::String, true, [null, false]];
        yield 'mariadb: littéral cité' => ["'l''été'", '', ColumnKind::String, true, ["l'été", false]];
        yield 'mariadb: nombre' => ['0.00', '', ColumnKind::Decimal, true, ['0.00', false]];
        yield 'mariadb: expression' => ['current_timestamp()', '', ColumnKind::DateTime, true, [null, true]];
    }

    /** @param array{string|null, bool} $expected */
    #[DataProvider('defaults')]
    public function testNormalizeDefault(mixed $raw, string $extra, ColumnKind $kind, bool $mariaDb, array $expected): void
    {
        self::assertSame($expected, MySqlSchemaReader::normalizeDefault($raw, strtolower($extra), $kind, $mariaDb));
    }

    public function testBuildColumn(): void
    {
        $column = MySqlSchemaReader::buildColumn([
            'column_name' => 'statut',
            'data_type' => 'enum',
            'column_type' => "enum('ouvert','clos')",
            'is_nullable' => 'NO',
            'column_default' => 'ouvert',
            'extra' => '',
            'max_length' => 6,
            'num_precision' => null,
            'num_scale' => null,
            'column_comment' => '',
        ], false);

        self::assertSame(ColumnKind::Enum, $column->kind);
        self::assertSame(['ouvert', 'clos'], $column->enumValues);
        self::assertSame('ouvert', $column->default);
        self::assertNull($column->maxLength, 'maxlength réservé aux chaînes');
        self::assertFalse($column->isRequired(), 'une valeur par défaut rend le champ facultatif');
    }

    public function testGeneratedColumnIsNotWritable(): void
    {
        $column = MySqlSchemaReader::buildColumn([
            'column_name' => 'total', 'data_type' => 'int', 'column_type' => 'int', 'is_nullable' => 'YES',
            'column_default' => null, 'extra' => 'STORED GENERATED', 'max_length' => null,
            'num_precision' => 10, 'num_scale' => 0, 'column_comment' => '',
        ], false);

        self::assertTrue($column->generated);
        self::assertFalse($column->isWritable());
    }

    public function testConnectRejectsNonMysqlDsn(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        MySqlSchemaReader::connect('sqlite::memory:', '', '');
    }
}
