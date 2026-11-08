<?php

declare(strict_types=1);

namespace Gene\Tests\Snapshot;

use Gene\Generator\GenerationOptions;
use Gene\Generator\GenerationResult;
use Gene\Generator\Generator;
use Gene\Schema\Column;
use Gene\Schema\ColumnKind;
use Gene\Schema\ForeignKey;
use Gene\Schema\Schema;
use Gene\Schema\Table;
use Gene\Tests\Fixtures\SchemaFixture;
use Gene\Tests\Support\Filesystem;
use PHPUnit\Framework\TestCase;

/**
 * Compare la sortie du générateur à tests/Snapshot/expected (fichiers propres à chaque schéma,
 * hors squelette). Après une évolution volontaire des gabarits :
 *     UPDATE_SNAPSHOTS=1 composer test
 * puis relisez le diff git des fichiers attendus.
 */
final class GeneratorSnapshotTest extends TestCase
{
    private const EXPECTED = __DIR__ . '/expected';

    private static ?GenerationResult $result = null;

    private static function generation(): GenerationResult
    {
        return self::$result ??= (new Generator())->generate(
            SchemaFixture::schema(),
            new GenerationOptions('Demo', 'mysql:host=127.0.0.1;port=3306;dbname=gene_test;charset=utf8mb4', 'gene'),
        );
    }

    /** @return array<string, string> fichiers générés, hors squelette copié tel quel */
    private static function generatedFiles(): array
    {
        $skeleton = __DIR__ . '/../../resources/skeleton/';

        return array_filter(self::generation()->files, static fn(string $path): bool => !is_file($skeleton . $path), ARRAY_FILTER_USE_KEY);
    }

    public function testWarnings(): void
    {
        self::assertSame([
            'Table « journal » ignorée : elle n\'a pas de clé primaire.',
            'Table « ligne_commande » : clé primaire composite, seule la liste est générée.',
        ], self::generation()->warnings);
        self::assertSame(4, self::generation()->entityCount);
    }

    public function testMatchesSnapshot(): void
    {
        $files = self::generatedFiles();

        if (getenv('UPDATE_SNAPSHOTS') === '1') {
            Filesystem::remove(self::EXPECTED);
            Filesystem::dump(self::EXPECTED, $files);
            self::markTestSkipped('Snapshots régénérés : relisez le diff.');
        }

        $expected = [];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(self::EXPECTED, \FilesystemIterator::SKIP_DOTS));
        foreach ($iterator as $file) {
            /** @var \SplFileInfo $file */
            $expected[substr($file->getPathname(), strlen(self::EXPECTED) + 1)] = (string) file_get_contents($file->getPathname());
        }
        ksort($expected);

        self::assertSame(array_keys($expected), array_keys($files), 'liste des fichiers générés');
        foreach ($expected as $path => $content) {
            self::assertSame($content, $files[$path], 'contenu de ' . $path);
        }
    }

    public function testEveryPhpFileIsValidAndGeneratedCodePassesPhpstan(): void
    {
        self::assertCompilesAndPassesPhpstan(self::generation());
    }

    /** Tables homonymes des classes du framework ou des espaces de noms : aucune collision d'import. */
    public function testFrameworkClassNamesAsTables(): void
    {
        $tables = [];
        foreach (['form', 'entity', 'app', 'repository', 'response', 'database', 'controller', 'framework', 'home', 'page', 'view', 'list'] as $name) {
            $tables[] = new Table($name, [
                new Column('id', ColumnKind::Int, 'int', autoIncrement: true, primary: true),
                new Column('nom', ColumnKind::String, 'varchar(20)', maxLength: 20),
                new Column('form_id', ColumnKind::Int, 'int', nullable: true),
            ], [new ForeignKey('fk_' . $name, 'form_id', 'form', 'id')]);
        }
        $result = (new Generator())->generate(new Schema('pieges', $tables), new GenerationOptions('Pieges'));

        self::assertSame(12, $result->entityCount);
        self::assertArrayHasKey('src/Controller/HomeTableController.php', $result->files, 'Home est réservé à l\'accueil');
        self::assertArrayHasKey('src/Entity/ListItem.php', $result->files, 'mot réservé PHP');
        self::assertCompilesAndPassesPhpstan($result);
    }

    /** Noms SQL hostiles : ils ne doivent ni casser ni injecter quoi que ce soit dans le code généré. */
    public function testHostileIdentifiersCannotInjectCode(): void
    {
        $hostile = ["a'b", 'x*/ echo 1; /*', '<script>alert(1)</script>', '$z{${phpinfo()}}', 'c`d', '{{ 7*7 }}', "l\\\\'f"];
        $columns = [new Column('id', ColumnKind::Int, 'int', autoIncrement: true, primary: true)];
        foreach ($hostile as $i => $name) {
            $columns[] = new Column($name, $i % 2 === 0 ? ColumnKind::String : ColumnKind::Enum, "enum('*/','<b>','''')", nullable: true, enumValues: ['*/', '<b>', "'"]);
        }
        $result = (new Generator())->generate(new Schema('pieges', [new Table("t'*/<i>", $columns)]), new GenerationOptions('Hostile'));

        foreach ($result->files as $path => $content) {
            if (str_starts_with($path, 'templates/t-i/')) {
                // HTML statique du gabarit (hors blocs PHP) : les noms y sont échappés à la génération.
                $html = (string) preg_replace('/<\?(?:php|=).*?\?>/s', '', $content);
                self::assertStringNotContainsString('<script>', $html, $path);
                self::assertStringNotContainsString('<b>', $html, $path);
            }
        }
        self::assertStringContainsString('{{ 7*7 }}', $result->files['templates/t-i/show.php'], 'les noms ne sont jamais évalués par Twig');
        self::assertCompilesAndPassesPhpstan($result);
    }

    private static function assertCompilesAndPassesPhpstan(GenerationResult $result): void
    {
        $dir = Filesystem::tempDir('gene-snapshot');
        try {
            Filesystem::dump($dir, $result->files);

            foreach ($result->files as $path => $content) {
                if (str_ends_with($path, '.php')) {
                    $out = [];
                    exec(escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($dir . '/' . $path) . ' 2>&1', $out, $code);
                    self::assertSame(0, $code, $path . "\n" . implode("\n", $out));
                }
            }

            file_put_contents($dir . '/phpstan.neon', "parameters:\n    level: 8\n    paths:\n        - src\n        - config\n");
            file_put_contents($dir . '/autoload.php', "<?php\nspl_autoload_register(static function (string \$c): void {\n    \$f = __DIR__ . '/src/' . str_replace('\\\\', '/', substr(\$c, 4)) . '.php';\n    if (str_starts_with(\$c, 'App\\\\') && is_file(\$f)) { require \$f; }\n});\n");
            $output = [];
            exec(sprintf(
                '%s %s analyse --no-progress --no-ansi --memory-limit=1G -c %s --autoload-file=%s 2>&1',
                escapeshellarg(PHP_BINARY),
                escapeshellarg(__DIR__ . '/../../vendor/bin/phpstan'),
                escapeshellarg($dir . '/phpstan.neon'),
                escapeshellarg($dir . '/autoload.php'),
            ), $output, $exit);
            self::assertSame(0, $exit, "PHPStan sur le code généré :\n" . implode("\n", $output));
        } finally {
            Filesystem::remove($dir);
        }
    }
}
