<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Command;

use Laminas\Config\Writer\WriterInterface;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Command\InitCommand;
use Shlinkio\Shlink\Installer\Command\UpdateCommand;
use Shlinkio\Shlink\Installer\Config\ConfigGeneratorInterface;
use Shlinkio\Shlink\Installer\Model\ImportedConfig;
use Shlinkio\Shlink\Installer\Service\ShlinkAssetsHandlerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Tester\CommandTester;

class UpdateCommandTest extends TestCase
{
    private CommandTester $commandTester;
    private MockObject & WriterInterface $configWriter;
    private MockObject & ShlinkAssetsHandlerInterface $assetsHandler;
    private MockObject & Command $initCommand;

    public function setUp(): void
    {
        $this->assetsHandler = $this->createMock(ShlinkAssetsHandlerInterface::class);
        $this->assetsHandler->expects($this->once())->method('dropCachedConfigIfAny');

        $this->configWriter = $this->createMock(WriterInterface::class);

        $generator = $this->createMock(ConfigGeneratorInterface::class);
        $generator->method('generateConfigInteractively')->willReturn([]);

        $app = new Application();
        $command = new UpdateCommand($this->configWriter, $this->assetsHandler, $generator);
        $app->add($command);

        $this->initCommand = $this->createMock(Command::class);
        $this->initCommand->method('getName')->willReturn(InitCommand::NAME);
        $this->initCommand->method('isEnabled')->willReturn(true);
        $app->add($this->initCommand);

        $this->commandTester = new CommandTester($command);
    }

    #[Test, DataProvider('provideCommands')]
    public function commandIsExecutedAsExpected(bool $rrBinExists, string $postUpdateCommands): void
    {
        $this->initCommand->expects($this->once())->method('run')->with(
            $this->callback(function (ArrayInput $input) use ($postUpdateCommands) {
                Assert::assertEquals(
                    $postUpdateCommands,
                    $input->__toString(),
                );
                return true;
            }),
            $this->anything(),
        )->willReturn(0);
        $this->assetsHandler->expects($this->once())->method('roadRunnerBinaryExistsInPath')->willReturn($rrBinExists);
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

    public static function provideCommands(): iterable
    {
        yield 'no rr binary' => [
            false,
            '--skip-initialize-db=1 --clear-db-cache=1 --initial-api-key --download-rr-binary',
        ];
        yield 'rr binary' => [
            true,
            '--skip-initialize-db=1 --clear-db-cache=1 --initial-api-key --download-rr-binary=1',
        ];
    }
}
