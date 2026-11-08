<?php

declare(strict_types=1);

namespace Gene\Tests\Unit;

use Gene\Config\Profile;
use Gene\Config\ProfileStore;
use Gene\Tests\Support\Filesystem;
use PHPUnit\Framework\TestCase;

final class ProfileStoreTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        $this->dir = Filesystem::tempDir('gene-profiles');
    }

    protected function tearDown(): void
    {
        Filesystem::remove($this->dir);
    }

    public function testRoundTripWithoutPassword(): void
    {
        $store = new ProfileStore($this->dir . '/sous-dossier');
        self::assertSame([], $store->all());

        $store->save('boutique', new Profile('db.local', '3307', 'dev', 'boutique', 'Boutique', '/srv/out', ['client', 'commande']));
        $store->save('autre', new Profile(database: 'x'));

        self::assertSame(['autre', 'boutique'], array_keys($store->all()));
        self::assertEquals(new Profile('db.local', '3307', 'dev', 'boutique', 'Boutique', '/srv/out', ['client', 'commande']), $store->get('boutique'));

        $json = (string) file_get_contents($store->path());
        self::assertStringNotContainsStringIgnoringCase('password', $json);
        self::assertStringNotContainsStringIgnoringCase('mdp', $json);
        if (PHP_OS_FAMILY !== 'Windows') {
            self::assertSame('0600', substr(sprintf('%o', fileperms($store->path())), -4));
        }

        self::assertTrue($store->delete('autre'));
        self::assertFalse($store->delete('autre'));
        self::assertSame(['boutique'], array_keys($store->all()));
    }

    public function testRejectsInvalidNames(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new ProfileStore($this->dir))->save('../evasion', new Profile());
    }

    public function testCorruptedFileIsIgnored(): void
    {
        file_put_contents($this->dir . '/profiles.json', '{pas du json');
        self::assertSame([], (new ProfileStore($this->dir))->all());
    }

    public function testDefaultDirHonoursEnvironment(): void
    {
        putenv('GENE_CONFIG_DIR=' . $this->dir);
        try {
            self::assertSame($this->dir, ProfileStore::defaultDir());
        } finally {
            putenv('GENE_CONFIG_DIR');
        }
        self::assertStringEndsWith('gene', ProfileStore::defaultDir());
    }
}
