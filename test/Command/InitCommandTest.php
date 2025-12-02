<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Command;

use PHPUnit\Framework\Assert;
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

class InitCommandTest extends TestCase
{
    private CommandTester $tester;
    private MockObject & InstallationCommandsRunnerInterface $commandsRunner;

    public function setUp(): void
    {
        $this->commandsRunner = $this->createMock(InstallationCommandsRunnerInterface::class);

        $app = new Application();
        $command = new InitCommand($this->commandsRunner);
        $app->addCommand($command);

        $this->tester = new CommandTester($command);
    }

    #[Test, DataProvider('provideInputs')]
    public function expectedCommandsAreRunBasedOnInput(array $input, array $commands, bool $interactive): void
    {
        $this->commandsRunner->expects($this->exactly(count($commands)))->method('execPhpCommand')->willReturnCallback(
            function (string $commandName, $_, bool $isInteractive, array $args) use ($commands, $interactive): bool {
                Assert::assertContains($commandName, $commands);
                Assert::assertEquals($interactive, $isInteractive);
                Assert::assertEquals($commandName === InstallationCommand::API_KEY_CREATE->value ? ['foo'] : [], $args);

                return true;
            },
        );

        $this->tester->execute($input, ['interactive' => $interactive]);
    }

    public static function provideInputs(): iterable
    {
        yield 'default' => [[], [
            InstallationCommand::DB_CREATE_SCHEMA->value,
            InstallationCommand::DB_MIGRATE->value,
            InstallationCommand::ORM_PROXIES->value,
            InstallationCommand::GEOLITE_DOWNLOAD_DB->value,
        ], true];
        yield 'non-interactive' => [[], [
            InstallationCommand::DB_CREATE_SCHEMA->value,
            InstallationCommand::DB_MIGRATE->value,
            InstallationCommand::ORM_PROXIES->value,
            InstallationCommand::GEOLITE_DOWNLOAD_DB->value,
        ], false];
        yield 'skips' => [['--skip-initialize-db' => true, '--skip-download-geolite' => true], [
            InstallationCommand::DB_MIGRATE->value,
            InstallationCommand::ORM_PROXIES->value,
        ], true];
        yield 'all' => [[
            '--clear-db-cache' => true,
            '--initial-api-key' => null,
            '--download-rr-binary' => true,
        ], [
            InstallationCommand::DB_CREATE_SCHEMA->value,
            InstallationCommand::DB_MIGRATE->value,
            InstallationCommand::ORM_PROXIES->value,
            InstallationCommand::ORM_CLEAR_CACHE->value,
            InstallationCommand::GEOLITE_DOWNLOAD_DB->value,
            InstallationCommand::API_KEY_GENERATE->value,
            InstallationCommand::ROAD_RUNNER_BINARY_DOWNLOAD->value,
        ], true];
        yield 'mixed' => [[
            '--initial-api-key' => null,
            '--skip-download-geolite' => true,
        ], [
            InstallationCommand::DB_CREATE_SCHEMA->value,
            InstallationCommand::DB_MIGRATE->value,
            InstallationCommand::ORM_PROXIES->value,
            InstallationCommand::API_KEY_GENERATE->value,
        ], true];
        yield 'api key value' => [[
            '--initial-api-key' => 'foo',
        ], [
            InstallationCommand::DB_CREATE_SCHEMA->value,
            InstallationCommand::DB_MIGRATE->value,
            InstallationCommand::ORM_PROXIES->value,
            InstallationCommand::API_KEY_CREATE->value,
            InstallationCommand::GEOLITE_DOWNLOAD_DB->value,
        ], true];
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
