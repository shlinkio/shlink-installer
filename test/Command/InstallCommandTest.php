<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Command;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Command\InitCommand;
use Shlinkio\Shlink\Installer\Command\InstallCommand;
use Shlinkio\Shlink\Installer\Config\ConfigGeneratorInterface;
use Shlinkio\Shlink\Installer\Service\ShlinkAssetsHandlerInterface;
use Shlinkio\Shlink\Installer\Util\ConfigWriterInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Tester\CommandTester;

class InstallCommandTest extends TestCase
{
    private CommandTester $commandTester;
    private MockObject & ConfigWriterInterface $configWriter;
    private MockObject & ShlinkAssetsHandlerInterface $assetsHandler;
    private MockObject & Command $initCommand;

    public function setUp(): void
    {
        $this->assetsHandler = $this->createMock(ShlinkAssetsHandlerInterface::class);
        $this->assetsHandler->expects($this->once())->method('dropCachedConfigIfAny');

        $this->configWriter = $this->createMock(ConfigWriterInterface::class);

        $configGenerator = $this->createMock(ConfigGeneratorInterface::class);
        $configGenerator->method('generateConfigInteractively')->willReturn([]);

        $app = new Application();
        $command = new InstallCommand(
            $this->configWriter,
            $this->assetsHandler,
            $configGenerator,
        );
        $app->addCommand($command);

        $this->initCommand = $this->createMock(Command::class);
        $this->initCommand->method('getName')->willReturn(InitCommand::NAME);
        $this->initCommand->method('isEnabled')->willReturn(true);
        $app->addCommand($this->initCommand);

        $this->commandTester = new CommandTester($command);
    }

    #[Test]
    public function commandIsExecutedAsExpected(): void
    {
        $this->initCommand->expects($this->once())->method('run')->with(
            $this->callback(function (ArrayInput $input) {
                Assert::assertEquals(
                    '--skip-initialize-db --clear-db-cache --download-rr-binary --initial-api-key',
                    $input->__toString(),
                );
                return true;
            }),
            $this->anything(),
        )->willReturn(0);
        $this->assetsHandler->expects($this->never())->method('resolvePreviousConfig');
        $this->assetsHandler->expects($this->never())->method('importShlinkAssetsFromPath');
        $this->configWriter->expects($this->once())->method('toFile')->with($this->anything(), $this->isArray());

        $this->commandTester->setInputs(['no']);
        $this->commandTester->execute([]);
    }
}
