<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Command;

use Laminas\Config\Writer\WriterInterface;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Command\UpdateCommand;
use Shlinkio\Shlink\Installer\Config\ConfigGeneratorInterface;
use Shlinkio\Shlink\Installer\Model\ImportedConfig;
use Shlinkio\Shlink\Installer\Service\InstallationCommandsRunnerInterface;
use Shlinkio\Shlink\Installer\Service\ShlinkAssetsHandlerInterface;
use Shlinkio\Shlink\Installer\Util\InstallationCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Process\PhpExecutableFinder;

use function count;

class UpdateCommandTest extends TestCase
{
    use ProphecyTrait;

    private CommandTester $commandTester;
    private ObjectProphecy $configWriter;
    private ObjectProphecy $assetsHandler;
    private ObjectProphecy $commandsRunner;

    public function setUp(): void
    {
        $this->assetsHandler = $this->prophesize(ShlinkAssetsHandlerInterface::class);
        $this->assetsHandler->dropCachedConfigIfAny(Argument::any())->shouldBeCalledOnce();

        $this->configWriter = $this->prophesize(WriterInterface::class);

        $this->commandsRunner = $this->prophesize(InstallationCommandsRunnerInterface::class);
        $this->commandsRunner->execPhpCommand(Argument::cetera())->willReturn(true);

        $config = new PathCollection();
        $configGenerator = $this->prophesize(ConfigGeneratorInterface::class);
        $configGenerator->generateConfigInteractively(Argument::cetera())->willReturn($config);

        $finder = $this->prophesize(PhpExecutableFinder::class);
        $finder->find(false)->willReturn('php');

        $app = new Application();
        $command = new UpdateCommand(
            $this->configWriter->reveal(),
            $this->assetsHandler->reveal(),
            $configGenerator->reveal(),
            $this->commandsRunner->reveal(),
        );
        $app->add($command);

        $this->commandTester = new CommandTester($command);
    }

    /** @test */
    public function commandIsExecutedAsExpected(): void
    {
        $execPhpCommand = $this->commandsRunner->execPhpCommand(
            Argument::that(function (string $command) {
                Assert::assertContains($command, InstallationCommand::POST_UPDATE_COMMANDS);

                return $command;
            }),
            Argument::cetera(),
        )->willReturn(true);
        $resolvePreviousCommand = $this->assetsHandler->resolvePreviousConfig(Argument::cetera())->willReturn(
            ImportedConfig::notImported(),
        );
        $importAssets = $this->assetsHandler->importShlinkAssetsFromPath(Argument::cetera());
        $persistConfig = $this->configWriter->toFile(Argument::any(), Argument::type('array'), false);

        $this->commandTester->setInputs(['no']);
        $this->commandTester->execute([]);

        $execPhpCommand->shouldHaveBeenCalledTimes(count(InstallationCommand::POST_UPDATE_COMMANDS));
        $resolvePreviousCommand->shouldHaveBeenCalledOnce();
        $importAssets->shouldHaveBeenCalledOnce();
        $persistConfig->shouldHaveBeenCalledOnce();
    }
}
