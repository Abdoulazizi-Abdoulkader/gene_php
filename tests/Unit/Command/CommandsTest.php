<?php

declare(strict_types=1);

namespace Gene\Tests\Unit\Command;

use Gene\Command\DoctorCommand;
use Gene\Command\GenerateCommand;
use Gene\Command\NewCommand;
use Gene\Command\ProfileDeleteCommand;
use Gene\Command\ProfileListCommand;
use Gene\Config\Profile;
use Gene\Config\ProfileStore;
use Gene\Tests\Support\Filesystem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class CommandsTest extends TestCase
{
    private string $dir;
    private ProfileStore $store;

    protected function setUp(): void
    {
        $this->dir = Filesystem::tempDir('gene-cmd');
        $this->store = new ProfileStore($this->dir);
    }

    protected function tearDown(): void
    {
        Filesystem::remove($this->dir);
    }

    public function testDoctorReportsEnvironment(): void
    {
        $tester = new CommandTester(new DoctorCommand(dirname(__DIR__, 3), $this->store));
        $code = $tester->execute([]);
        $display = $tester->getDisplay();

        self::assertStringContainsString('PHP ≥ 8.3', $display);
        self::assertStringContainsString('extension pdo_mysql', $display);
        self::assertSame(extension_loaded('pdo_mysql') && extension_loaded('mbstring') ? Command::SUCCESS : Command::FAILURE, $code);
    }

    public function testDoctorExplainsConnectionFailure(): void
    {
        $tester = new CommandTester(new DoctorCommand(dirname(__DIR__, 3), $this->store));
        $tester->execute(['--connect' => true, '--host' => '127.0.0.1', '--port' => '1', '--no-password' => true], ['interactive' => false]);

        self::assertSame(Command::FAILURE, $tester->getStatusCode());
        self::assertStringContainsString('injoignable', $tester->getDisplay());
    }

    public function testWizardRefusesNonInteractiveMode(): void
    {
        $tester = new CommandTester(new NewCommand(sys_get_temp_dir(), $this->store));
        self::assertSame(Command::INVALID, $tester->execute([], ['interactive' => false]));
        self::assertStringContainsString('gene generate', $tester->getDisplay());
    }

    public function testGenerateExplainsMissingDatabase(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/assistant/');
        (new CommandTester(new GenerateCommand(sys_get_temp_dir(), $this->store)))->execute([], ['interactive' => false]);
    }

    public function testUnknownProfile(): void
    {
        $this->expectExceptionMessage('Profil « absent » inconnu');
        (new CommandTester(new GenerateCommand(sys_get_temp_dir(), $this->store)))->execute(['--profile' => 'absent'], ['interactive' => false]);
    }

    public function testProfileCommands(): void
    {
        $this->store->save('boutique', new Profile(database: 'boutique', app: 'Boutique'));

        $list = new CommandTester(new ProfileListCommand($this->store));
        $list->execute([]);
        self::assertStringContainsString('boutique', $list->getDisplay());
        self::assertStringContainsString('toutes', $list->getDisplay());

        $delete = new CommandTester(new ProfileDeleteCommand($this->store));
        self::assertSame(Command::SUCCESS, $delete->execute(['name' => 'boutique']));
        self::assertSame(Command::FAILURE, $delete->execute(['name' => 'boutique']));
        self::assertSame([], $this->store->all());
    }
}
