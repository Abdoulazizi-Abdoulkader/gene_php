<?php

declare(strict_types=1);

namespace Gene\Tests\Integration;

use Gene\Command\GenerateCommand;
use Gene\Command\NewCommand;
use Gene\Config\ProfileStore;
use Gene\Generator\FileWriter;
use Gene\Tests\Support\Filesystem;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * L'assistant, piloté comme par un utilisateur, contre une vraie base
 * (mêmes variables que EndToEndTest ; le schéma de test doit être chargé).
 */
#[Group('integration')]
final class WizardTest extends TestCase
{
    private string $dir;
    private ProfileStore $store;
    /** @var array{host: string, port: string, database: string, user: string, password: string} */
    private array $db;

    protected function setUp(): void
    {
        $dsn = getenv('GENE_TEST_DSN');
        if (!is_string($dsn) || preg_match('/host=([^;]+).*?port=(\d+).*?dbname=([^;]+)/', $dsn, $m) !== 1) {
            self::markTestSkipped('GENE_TEST_DSN (avec host, port et dbname) non défini : test d\'intégration ignoré.');
        }
        $this->db = ['host' => $m[1], 'port' => $m[2], 'database' => $m[3], 'user' => (string) getenv('GENE_TEST_USER'), 'password' => (string) getenv('GENE_TEST_PASSWORD')];
        $this->dir = Filesystem::tempDir('gene-wizard');
        $this->store = new ProfileStore($this->dir . '/config');
    }

    protected function tearDown(): void
    {
        if (isset($this->dir)) {
            Filesystem::remove($this->dir);
        }
    }

    public function testWizardGeneratesSavesProfileAndProfileReplays(): void
    {
        $out = $this->dir . '/Boutique';
        $tester = new CommandTester(new NewCommand($this->dir, $this->store));
        $tester->setInputs([
            $this->db['host'], $this->db['port'], 'mauvais-compte', 'x', // 1er essai : accès refusé
            $this->db['host'], $this->db['port'], $this->db['user'], $this->db['password'],
            $this->db['database'],   // base
            'client,pays',           // tables
            'Boutique',              // application
            $out,                    // dossier
            'oui',                   // générer
            'o', 'boutique',         // profil
            'n',                     // mot de passe dans .env
        ]);
        $code = $tester->execute([]);
        $display = $tester->getDisplay();

        self::assertSame(Command::SUCCESS, $code, $display);
        self::assertStringContainsString('Accès refusé', $display, 'erreur de connexion expliquée, puis nouvel essai');
        self::assertStringContainsString('✔ Connecté', $display);
        self::assertFileExists($out . '/src/Controller/ClientController.php');
        self::assertFileExists($out . '/src/Controller/PaysController.php');
        self::assertFileDoesNotExist($out . '/src/Controller/CommandeController.php', 'table non choisie');
        self::assertStringContainsString('DB_PASSWORD=""', (string) file_get_contents($out . '/.env'), 'mot de passe non écrit sans accord');

        $profile = $this->store->get('boutique');
        self::assertNotNull($profile);
        self::assertSame(['client', 'pays'], $profile->tables);
        self::assertSame('Boutique', $profile->app);
        self::assertStringNotContainsString($this->db['password'] !== '' ? $this->db['password'] : "\0", (string) file_get_contents($this->store->path()));

        // Relance non interactive depuis le profil : rien ne change.
        putenv('GENE_DB_PASSWORD=' . $this->db['password']);
        try {
            $generate = new CommandTester(new GenerateCommand($this->dir, $this->store));
            self::assertSame(Command::SUCCESS, $generate->execute(['--profile' => 'boutique'], ['interactive' => false]), $generate->getDisplay());
        } finally {
            putenv('GENE_DB_PASSWORD');
        }
        self::assertMatchesRegularExpression('/inchangé\s+\d+/', $generate->getDisplay());
        self::assertFileExists($out . '/' . FileWriter::MANIFEST);
    }
}
