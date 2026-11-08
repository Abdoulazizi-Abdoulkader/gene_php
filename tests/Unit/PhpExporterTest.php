<?php

declare(strict_types=1);

namespace Gene\Tests\Unit;

use Gene\Generator\PhpExporter;
use PHPUnit\Framework\TestCase;

final class PhpExporterTest extends TestCase
{
    public function testHostileStringsStayInsideTheLiteral(): void
    {
        foreach (["'; system('id'); //", 'a\\', "\\'", "x\ny", '<?php ?>', '${x}', '?>'] as $value) {
            $code = PhpExporter::export($value);
            self::assertSame($value, eval('return ' . $code . ';'), $code);
        }
    }

    public function testArrays(): void
    {
        self::assertSame('[]', PhpExporter::export([]));
        self::assertSame("['a', 'b']", PhpExporter::export(['a', 'b']));
        self::assertSame("[\n    'k' => true,\n    'n' => null,\n]", PhpExporter::export(['k' => true, 'n' => null]));

        $nested = ['x' => ['label' => "l'été", 'choices' => ['a']]];
        self::assertSame($nested, eval('return ' . PhpExporter::export($nested, 1) . ';'));
    }

    public function testScalars(): void
    {
        self::assertSame('42', PhpExporter::export(42));
        self::assertSame('false', PhpExporter::export(false));
        self::assertSame('1.5', PhpExporter::export(1.5));
    }
}
