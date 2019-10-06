<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Command;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use ReflectionObject;
use Shlinkio\Shlink\Installer\Command\InstallCommand;
use Shlinkio\Shlink\Installer\Config\ConfigCustomizerManagerInterface;
use Shlinkio\Shlink\Installer\Config\Plugin\ConfigCustomizerInterface;
use Shlinkio\Shlink\Installer\Service\InstallationCommandsRunnerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\PhpExecutableFinder;
use Zend\Config\Writer\WriterInterface;

class InstallCommandTest extends TestCase
{
    /** @var InstallCommand */
    private $command;
    /** @var CommandTester */
    private $commandTester;
    /** @var ObjectProphecy */
    private $configWriter;
    /** @var ObjectProphecy */
    private $filesystem;
    /** @var ObjectProphecy */
    private $commandsRunner;

    public function setUp(): void
    {
        $this->filesystem = $this->prophesize(Filesystem::class);
        $this->filesystem->exists(Argument::cetera())->willReturn(false);

        $this->configWriter = $this->prophesize(WriterInterface::class);

        $this->commandsRunner = $this->prophesize(InstallationCommandsRunnerInterface::class);
        $this->commandsRunner->execPhpCommand(Argument::cetera())->willReturn(true);

        $configCustomizer = $this->prophesize(ConfigCustomizerInterface::class);
        $configCustomizers = $this->prophesize(ConfigCustomizerManagerInterface::class);
        $configCustomizers->get(Argument::cetera())->willReturn($configCustomizer->reveal());

        $finder = $this->prophesize(PhpExecutableFinder::class);
        $finder->find(false)->willReturn('php');

        $app = new Application();
        $this->command = new InstallCommand(
            $this->configWriter->reveal(),
            $this->filesystem->reveal(),
            $configCustomizers->reveal(),
            $this->commandsRunner->reveal(),
            false
        );
        $app->add($this->command);

        $this->commandTester = new CommandTester($this->command);
    }

    /** @test */
    public function generatedConfigIsProperlyPersisted(): void
    {
        $this->configWriter->toFile(Argument::any(), Argument::type('array'), false)->shouldBeCalledOnce();
        $this->commandTester->execute([]);
    }

    /** @test */
    public function cachedConfigIsDeletedIfExists(): void
    {
        $appConfigExists = $this->filesystem->exists('data/cache/app_config.php')->willReturn(true);
        $appConfigRemove = $this->filesystem->remove('data/cache/app_config.php')->willReturn(null);

        $this->commandTester->execute([]);

        $appConfigExists->shouldHaveBeenCalledOnce();
        $appConfigRemove->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function exceptionWhileDeletingCachedConfigCancelsProcess(): void
    {
        $appConfigExists = $this->filesystem->exists('data/cache/app_config.php')->willReturn(true);
        $appConfigRemove = $this->filesystem->remove('data/cache/app_config.php')->willThrow(IOException::class);
        $configToFile = $this->configWriter->toFile(Argument::cetera())->willReturn(true);

        $this->commandTester->execute([]);

        $appConfigExists->shouldHaveBeenCalledOnce();
        $appConfigRemove->shouldHaveBeenCalledOnce();
        $configToFile->shouldNotHaveBeenCalled();
    }

    /** @test */
    public function whenCommandIsUpdatePreviousConfigCanBeImported(): void
    {
        $ref = new ReflectionObject($this->command);
        $prop = $ref->getProperty('isUpdate');
        $prop->setAccessible(true);
        $prop->setValue($this->command, true);

        $importedConfigExists = $this->filesystem->exists(
            __DIR__ . '/../../test-resources/' . InstallCommand::GENERATED_CONFIG_PATH
        )->willReturn(true);

        $this->commandTester->setInputs([
            '',
            '/foo/bar/wrong_previous_shlink',
            '',
            __DIR__ . '/../../test-resources',
        ]);
        $this->commandTester->execute([]);

        $importedConfigExists->shouldHaveBeenCalled();
    }

    /**
     * @test
     * @dataProvider provideAmounts
     */
    public function commandRunnerIsInvokedTheProperAmountOfTimes(bool $isUpdate, int $expectedAmount): void
    {
        $ref = new ReflectionObject($this->command);
        $prop = $ref->getProperty('isUpdate');
        $prop->setAccessible(true);
        $prop->setValue($this->command, $isUpdate);
        $this->filesystem->exists('data/cache/app_config.php')->willReturn(false);

        $execPhpCommand = $this->commandsRunner->execPhpCommand(Argument::cetera())->willReturn(true);

        $this->commandTester->setInputs(['no']);
        $this->commandTester->execute([]);

        $execPhpCommand->shouldHaveBeenCalledTimes($expectedAmount);
    }

    public function provideAmounts(): iterable
    {
        yield [false, 4];
        yield [true, 3];
    }
}
