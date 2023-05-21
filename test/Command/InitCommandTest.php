<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Command;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Command\InitCommand;
use Shlinkio\Shlink\Installer\Service\InstallationCommandsRunnerInterface;
use Shlinkio\Shlink\Installer\Util\InstallationCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

use function count;
use function Functional\contains;

class InitCommandTest extends TestCase
{
    private CommandTester $tester;
    private MockObject & InstallationCommandsRunnerInterface $commandsRunner;

    public function setUp(): void
    {
        $this->commandsRunner = $this->createMock(InstallationCommandsRunnerInterface::class);

        $app = new Application();
        $command = new InitCommand($this->commandsRunner);
        $app->add($command);

        $this->tester = new CommandTester($command);
    }

    #[Test, DataProvider('provideInputs')]
    public function expectedCommandsAreRunBasedOnInput(array $input, array $expectedCommands): void
    {
        $this->commandsRunner->expects($this->exactly(count($expectedCommands)))->method('execPhpCommand')->with(
            $this->callback(fn (string $commandName) => contains($expectedCommands, $commandName)),
            $this->anything(),
        )->willReturn(true);

        $this->tester->execute($input);
    }

    public static function provideInputs(): iterable
    {
        yield 'default' => [[], [
            InstallationCommand::DB_CREATE_SCHEMA->value,
            InstallationCommand::DB_MIGRATE->value,
            InstallationCommand::ORM_PROXIES->value,
            InstallationCommand::GEOLITE_DOWNLOAD_DB->value,
        ]];
        yield 'skips' => [['--skip-initialize-db' => true, '--skip-download-geolite' => true], [
            InstallationCommand::DB_MIGRATE->value,
            InstallationCommand::ORM_PROXIES->value,
        ]];
        yield 'all' => [[
            '--clear-db-cache' => true,
            '--initial-api-key' => true,
            '--update-roadrunner-binary' => true,
        ], [
            InstallationCommand::DB_CREATE_SCHEMA->value,
            InstallationCommand::DB_MIGRATE->value,
            InstallationCommand::ORM_PROXIES->value,
            InstallationCommand::ORM_CLEAR_CACHE->value,
            InstallationCommand::GEOLITE_DOWNLOAD_DB->value,
            InstallationCommand::API_KEY_GENERATE->value,
            InstallationCommand::ROAD_RUNNER_BINARY_DOWNLOAD->value,
        ]];
        yield 'mixed' => [[
            '--initial-api-key' => true,
            '--skip-download-geolite' => true,
        ], [
            InstallationCommand::DB_CREATE_SCHEMA->value,
            InstallationCommand::DB_MIGRATE->value,
            InstallationCommand::ORM_PROXIES->value,
            InstallationCommand::API_KEY_GENERATE->value,
        ]];
    }

    #[Test, DataProvider('provideExitCodes')]
    public function properExitCodeIsReturnedBasedOnCommandsExecution(bool $result, int $expectedExitCode): void
    {
        $this->commandsRunner->method('execPhpCommand')->willReturn($result);
        $exitCode = $this->tester->execute([]);

        self::assertEquals($expectedExitCode, $exitCode);
    }

    public static function provideExitCodes(): iterable
    {
        yield 'success' => [true, 0];
        yield 'error' => [false, -1];
    }
}
