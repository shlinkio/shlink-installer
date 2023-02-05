<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Command;

use Laminas\Config\Writer\WriterInterface;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Command\UpdateCommand;
use Shlinkio\Shlink\Installer\Config\ConfigGeneratorInterface;
use Shlinkio\Shlink\Installer\Model\ImportedConfig;
use Shlinkio\Shlink\Installer\Service\InstallationCommandsRunnerInterface;
use Shlinkio\Shlink\Installer\Service\ShlinkAssetsHandlerInterface;
use Shlinkio\Shlink\Installer\Util\InstallationCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

use function count;
use function Functional\map;

class UpdateCommandTest extends TestCase
{
    private CommandTester $commandTester;
    private MockObject & WriterInterface $configWriter;
    private MockObject & ShlinkAssetsHandlerInterface $assetsHandler;
    private MockObject & InstallationCommandsRunnerInterface $commandsRunner;

    public function setUp(): void
    {
        $this->assetsHandler = $this->createMock(ShlinkAssetsHandlerInterface::class);
        $this->assetsHandler->expects($this->once())->method('dropCachedConfigIfAny');

        $this->configWriter = $this->createMock(WriterInterface::class);
        $this->commandsRunner = $this->createMock(InstallationCommandsRunnerInterface::class);

        $generator = $this->createMock(ConfigGeneratorInterface::class);
        $generator->method('generateConfigInteractively')->willReturn([]);

        $app = new Application();
        $command = new UpdateCommand($this->configWriter, $this->assetsHandler, $generator, $this->commandsRunner);
        $app->add($command);

        $this->commandTester = new CommandTester($command);
    }

    #[Test]
    public function commandIsExecutedAsExpected(): void
    {
        $this->commandsRunner->expects(
            $this->exactly(count(InstallationCommand::POST_UPDATE_COMMANDS)),
        )->method('execPhpCommand')->with(
            $this->callback(function (string $commandName) {
                Assert::assertContains($commandName, map(
                    InstallationCommand::POST_UPDATE_COMMANDS,
                    fn (InstallationCommand $command) => $command->value,
                ));
                return true;
            }),
            $this->anything(),
        )->willReturn(true);
        $this->assetsHandler->expects($this->once())->method('resolvePreviousConfig')->willReturn(
            ImportedConfig::notImported(),
        );
        $this->assetsHandler->expects($this->once())->method('importShlinkAssetsFromPath');
        $this->configWriter->expects($this->once())->method('toFile')->with(
            $this->anything(),
            $this->isType('array'),
            false,
        );

        $this->commandTester->setInputs(['no']);
        $this->commandTester->execute([]);
    }
}
