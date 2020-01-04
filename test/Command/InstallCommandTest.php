<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Command;

use Laminas\Config\Writer\WriterInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use ReflectionObject;
use Shlinkio\Shlink\Installer\Command\InstallCommand;
use Shlinkio\Shlink\Installer\Config\ConfigGeneratorInterface;
use Shlinkio\Shlink\Installer\Config\Option\DatabaseDriverConfigOption;
use Shlinkio\Shlink\Installer\Service\InstallationCommandsRunnerInterface;
use Shlinkio\Shlink\Installer\Util\PathCollection;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\PhpExecutableFinder;

class InstallCommandTest extends TestCase
{
    private InstallCommand $command;
    private CommandTester $commandTester;
    private ObjectProphecy $configWriter;
    private ObjectProphecy $filesystem;
    private ObjectProphecy $commandsRunner;
    private PathCollection $config;

    public function setUp(): void
    {
        $this->filesystem = $this->prophesize(Filesystem::class);
        $this->filesystem->exists(Argument::cetera())->willReturn(false);

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
            $this->filesystem->reveal(),
            $configGenerator->reveal(),
            $this->commandsRunner->reveal(),
            false,
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

        $appConfigExists->shouldBeCalledOnce();
        $appConfigRemove->shouldBeCalledOnce();
        $configToFile->shouldNotBeCalled();

        $this->expectException(IOException::class);

        $this->commandTester->execute([]);
    }

    /** @test */
    public function whenCommandIsUpdatePreviousConfigCanBeImported(): void
    {
        $this->setIsUpdate();

        $importedConfigExists = $this->filesystem->exists(
            __DIR__ . '/../../test-resources/' . InstallCommand::GENERATED_CONFIG_PATH,
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

    /** @test */
    public function sqliteDatabaseIsImportedOnUpdate(): void
    {
        $this->setIsUpdate();
        $this->config->setValueInPath(
            DatabaseDriverConfigOption::SQLITE_DRIVER,
            DatabaseDriverConfigOption::CONFIG_PATH,
        );

        $copy = $this->filesystem->copy(
            __DIR__ . '/../../test-resources/data/database.sqlite',
            'data/database.sqlite',
        )->will(function (): void {
        });
        $importedConfigExists = $this->filesystem->exists(
            __DIR__ . '/../../test-resources/' . InstallCommand::GENERATED_CONFIG_PATH,
        )->willReturn(true);

        $this->commandTester->setInputs([
            '',
            __DIR__ . '/../../test-resources',
        ]);
        $this->commandTester->execute([]);

        $importedConfigExists->shouldHaveBeenCalledOnce();
        $copy->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function processIsCancelledWhenSqliteImportFails(): void
    {
        $this->setIsUpdate();
        $this->config->setValueInPath(
            DatabaseDriverConfigOption::SQLITE_DRIVER,
            DatabaseDriverConfigOption::CONFIG_PATH,
        );

        $copy = $this->filesystem->copy(
            __DIR__ . '/../../test-resources/data/database.sqlite',
            'data/database.sqlite',
        )->willThrow(IOException::class);
        $importedConfigExists = $this->filesystem->exists(
            __DIR__ . '/../../test-resources/' . InstallCommand::GENERATED_CONFIG_PATH,
        )->willReturn(true);

        $importedConfigExists->shouldBeCalledOnce();
        $copy->shouldBeCalledOnce();

        $this->expectException(IOException::class);

        $this->commandTester->setInputs([
            '',
            __DIR__ . '/../../test-resources',
        ]);
        $this->commandTester->execute([]);
    }

    /**
     * @test
     * @dataProvider provideAmounts
     */
    public function commandRunnerIsInvokedTheProperAmountOfTimes(bool $isUpdate, int $expectedAmount): void
    {
        $this->setIsUpdate($isUpdate);
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

    private function setIsUpdate(bool $isUpdate = true): void
    {
        $ref = new ReflectionObject($this->command);
        $prop = $ref->getProperty('isUpdate');
        $prop->setAccessible(true);
        $prop->setValue($this->command, $isUpdate);
    }
}
