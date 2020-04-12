<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Command;

use Laminas\Config\Writer\WriterInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use ReflectionObject;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Command\InstallCommand;
use Shlinkio\Shlink\Installer\Config\ConfigGeneratorInterface;
use Shlinkio\Shlink\Installer\Model\ImportedConfig;
use Shlinkio\Shlink\Installer\Service\InstallationCommandsRunnerInterface;
use Shlinkio\Shlink\Installer\Service\ShlinkAssetsHandlerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Process\PhpExecutableFinder;

class InstallCommandTest extends TestCase
{
    use ProphecyTrait;

    private InstallCommand $command;
    private CommandTester $commandTester;
    private ObjectProphecy $configWriter;
    private ObjectProphecy $assetsHandler;
    private ObjectProphecy $commandsRunner;
    private PathCollection $config;

    public function setUp(): void
    {
        $this->assetsHandler = $this->prophesize(ShlinkAssetsHandlerInterface::class);
        $this->assetsHandler->dropCachedConfigIfAny(Argument::any())->shouldBeCalledOnce();

        $this->configWriter = $this->prophesize(WriterInterface::class);

        $this->commandsRunner = $this->prophesize(InstallationCommandsRunnerInterface::class);
        $this->commandsRunner->execPhpCommand(Argument::cetera())->willReturn(true);

        $this->config = new PathCollection();
        $configGenerator = $this->prophesize(ConfigGeneratorInterface::class);
        $configGenerator->generateConfigInteractively(Argument::cetera())->willReturn($this->config);

        $finder = $this->prophesize(PhpExecutableFinder::class);
        $finder->find(false)->willReturn('php');

        $app = new Application();
        $this->command = new InstallCommand(
            $this->configWriter->reveal(),
            $this->assetsHandler->reveal(),
            $configGenerator->reveal(),
            $this->commandsRunner->reveal(),
            false,
        );
        $app->add($this->command);

        $this->commandTester = new CommandTester($this->command);
    }

    /**
     * @test
     * @dataProvider provideAmounts
     */
    public function commandIsExecutedAsExpected(bool $isUpdate): void
    {
        $this->setIsUpdate($isUpdate);

        $execPhpCommand = $this->commandsRunner->execPhpCommand(Argument::cetera())->willReturn(true);
        $resolvePreviousCommand = $this->assetsHandler->resolvePreviousConfig(Argument::cetera())->willReturn(
            ImportedConfig::notImported(),
        );
        $importAssets = $this->assetsHandler->importShlinkAssetsFromPath(Argument::cetera());
        $persistConfig = $this->configWriter->toFile(Argument::any(), Argument::type('array'), false);

        $this->commandTester->setInputs(['no']);
        $this->commandTester->execute([]);

        $execPhpCommand->shouldHaveBeenCalledTimes($isUpdate ? 3 : 4);
        $resolvePreviousCommand->shouldHaveBeenCalledTimes($isUpdate ? 1 : 0);
        $importAssets->shouldHaveBeenCalledTimes($isUpdate ? 1 : 0);
        $persistConfig->shouldHaveBeenCalledOnce();
    }

    public function provideAmounts(): iterable
    {
        yield 'is update' => [true];
        yield 'is not update' => [false];
    }

    private function setIsUpdate(bool $isUpdate = true): void
    {
        $ref = new ReflectionObject($this->command);
        $prop = $ref->getProperty('isUpdate');
        $prop->setAccessible(true);
        $prop->setValue($this->command, $isUpdate);
    }
}
