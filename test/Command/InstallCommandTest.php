<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Command;

use Laminas\Config\Writer\WriterInterface;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Command\InstallCommand;
use Shlinkio\Shlink\Installer\Config\ConfigGeneratorInterface;
use Shlinkio\Shlink\Installer\Service\InstallationCommandsRunnerInterface;
use Shlinkio\Shlink\Installer\Service\ShlinkAssetsHandlerInterface;
use Shlinkio\Shlink\Installer\Util\InstallationCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

use function count;
use function Functional\map;

class InstallCommandTest extends TestCase
{
    private CommandTester $commandTester;
    private MockObject $configWriter;
    private MockObject $assetsHandler;
    private MockObject $commandsRunner;

    public function setUp(): void
    {
        $this->assetsHandler = $this->createMock(ShlinkAssetsHandlerInterface::class);
        $this->assetsHandler->expects($this->once())->method('dropCachedConfigIfAny');

        $this->configWriter = $this->createMock(WriterInterface::class);
        $this->commandsRunner = $this->createMock(InstallationCommandsRunnerInterface::class);

        $configGenerator = $this->createMock(ConfigGeneratorInterface::class);
        $configGenerator->method('generateConfigInteractively')->willReturn([]);

        $app = new Application();
        $command = new InstallCommand(
            $this->configWriter,
            $this->assetsHandler,
            $configGenerator,
            $this->commandsRunner,
        );
        $app->add($command);

        $this->commandTester = new CommandTester($command);
    }

    #[Test]
    public function commandIsExecutedAsExpected(): void
    {
        $postInstallCommands = [
            InstallationCommand::DB_CREATE_SCHEMA,
            InstallationCommand::DB_MIGRATE,
            InstallationCommand::ORM_PROXIES,
            InstallationCommand::GEOLITE_DOWNLOAD_DB,
            InstallationCommand::API_KEY_GENERATE,
        ];

        $this->commandsRunner->expects($this->exactly(count($postInstallCommands)))->method('execPhpCommand')->with(
            $this->callback(function (string $commandName) use ($postInstallCommands) {
                Assert::assertContains($commandName, map(
                    $postInstallCommands,
                    fn (InstallationCommand $command) => $command->value,
                ));
                return true;
            }),
            $this->anything(),
        )->willReturn(true);
        $this->assetsHandler->expects($this->never())->method('resolvePreviousConfig');
        $this->assetsHandler->expects($this->never())->method('importShlinkAssetsFromPath');
        $this->configWriter->expects($this->once())->method('toFile')->with(
            $this->anything(),
            $this->isType('array'),
            false,
        );

        $this->commandTester->setInputs(['no']);
        $this->commandTester->execute([]);
    }
}
