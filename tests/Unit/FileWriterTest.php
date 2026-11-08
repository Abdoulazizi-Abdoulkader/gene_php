<?php

declare(strict_types=1);

namespace Gene\Tests\Unit;

use Gene\Generator\FileWriter;
use Gene\Generator\WriteAction;
use Gene\Tests\Support\Filesystem;
use PHPUnit\Framework\TestCase;

final class FileWriterTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . '/gene-fw-' . bin2hex(random_bytes(4));
    }

    protected function tearDown(): void
    {
        Filesystem::remove($this->dir);
    }

    public function testLifecycle(): void
    {
        $first = (new FileWriter($this->dir))->write(['a.php' => 'v1', 'sub/b.php' => 'v1']);
        self::assertSame(['a.php' => WriteAction::Created, 'sub/b.php' => WriteAction::Created], $first->all());
        self::assertFileExists($this->dir . '/' . FileWriter::MANIFEST);

        // b.php est modifié à la main ; la génération suivante change les deux fichiers.
        file_put_contents($this->dir . '/sub/b.php', 'travail du développeur');
        $second = (new FileWriter($this->dir))->write(['a.php' => 'v2', 'sub/b.php' => 'v2']);
        self::assertSame(WriteAction::Updated, $second->all()['a.php']);
        self::assertSame(WriteAction::Skipped, $second->all()['sub/b.php']);
        self::assertSame('travail du développeur', file_get_contents($this->dir . '/sub/b.php'));

        // Le fichier reste protégé aux générations suivantes, jusqu'à --force.
        $third = (new FileWriter($this->dir))->write(['a.php' => 'v2', 'sub/b.php' => 'v3']);
        self::assertSame(WriteAction::Unchanged, $third->all()['a.php']);
        self::assertSame(WriteAction::Skipped, $third->all()['sub/b.php']);

        $forced = (new FileWriter($this->dir, force: true))->write(['sub/b.php' => 'v3']);
        self::assertSame(WriteAction::Overwritten, $forced->all()['sub/b.php']);
        self::assertSame('v3', file_get_contents($this->dir . '/sub/b.php'));
    }

    public function testPreexistingUnknownFileIsKept(): void
    {
        mkdir($this->dir);
        file_put_contents($this->dir . '/README.md', 'à moi');
        $report = (new FileWriter($this->dir))->write(['README.md' => 'généré']);
        self::assertSame(WriteAction::Skipped, $report->all()['README.md']);
        self::assertSame('à moi', file_get_contents($this->dir . '/README.md'));
    }

    public function testDryRunWritesNothing(): void
    {
        $report = (new FileWriter($this->dir, dryRun: true))->write(['a.php' => 'x']);
        self::assertSame(WriteAction::Created, $report->all()['a.php']);
        self::assertDirectoryDoesNotExist($this->dir);
    }

    public function testRefusesToWriteThroughSymlink(): void
    {
        mkdir($this->dir);
        $outside = $this->dir . '-dehors';
        mkdir($outside);
        symlink($outside, $this->dir . '/src');
        try {
            (new FileWriter($this->dir))->write(['src/Entity/a.php' => 'x']);
            self::fail('écriture à travers un lien symbolique acceptée');
        } catch (\RuntimeException) {
            self::assertDirectoryDoesNotExist($outside . '/Entity');
        } finally {
            unlink($this->dir . '/src');
            rmdir($outside);
        }
    }

    public function testRejectsPathTraversal(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new FileWriter($this->dir))->write(['../evil.php' => 'x']);
    }
}
