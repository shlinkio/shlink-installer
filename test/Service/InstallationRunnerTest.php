<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Service;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\ConfigGeneratorInterface;
use Shlinkio\Shlink\Installer\Model\ImportedConfig;
use Shlinkio\Shlink\Installer\Service\InstallationRunner;
use Shlinkio\Shlink\Installer\Service\ShlinkAssetsHandlerInterface;
use Shlinkio\Shlink\Installer\Util\ConfigWriterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Style\SymfonyStyle;

class InstallationRunnerTest extends TestCase
{
    private MockObject & ConfigWriterInterface $configWriter;
    private MockObject & ShlinkAssetsHandlerInterface $assetsHandler;
    private MockObject & Command $initCommand;
    private InstallationRunner $installationRunner;

    protected function setUp(): void
    {
        $this->initCommand = $this->createMock(Command::class);
        $this->assetsHandler = $this->createMock(ShlinkAssetsHandlerInterface::class);
        $this->configWriter = $this->createMock(ConfigWriterInterface::class);

        $configGenerator = $this->createStub(ConfigGeneratorInterface::class);
        $configGenerator->method('generateConfigInteractively')->willReturn([]);

        $this->installationRunner = new InstallationRunner($this->configWriter, $this->assetsHandler, $configGenerator);
    }

    #[Test]
    public function installationIsExecutedAsExpected(): void
    {
        $this->initCommand->expects($this->once())->method('run')->with(
            $this->callback(function (ArrayInput $input) {
                Assert::assertEquals('--initial-api-key', $input->__toString());
                return true;
            }),
            $this->anything(),
        )->willReturn(Command::SUCCESS);

        $this->assetsHandler->expects($this->never())->method('dropCachedConfigIfAny');
        $this->assetsHandler->expects($this->never())->method('resolvePreviousConfig');
        $this->assetsHandler->expects($this->never())->method('roadRunnerBinaryExistsInPath');
        $this->assetsHandler->expects($this->never())->method('importShlinkAssetsFromPath');

        $this->configWriter->expects($this->once())->method('toFile')->with($this->anything(), $this->isArray());

        $this->installationRunner->runInstallation($this->createStub(SymfonyStyle::class), $this->initCommand);
    }

    #[Test, DataProvider('provideCommands')]
    public function updateIsExecutedAsExpected(bool $rrBinExists, string $postUpdateCommands): void
    {
        $this->initCommand->expects($this->once())->method('run')->with(
            $this->callback(function (ArrayInput $input) use ($postUpdateCommands) {
                Assert::assertEquals($postUpdateCommands, $input->__toString());
                return true;
            }),
            $this->anything(),
        )->willReturn(0);

        $this->assetsHandler->expects($this->once())->method('dropCachedConfigIfAny');
        $this->assetsHandler->expects($this->once())->method('resolvePreviousConfig')->willReturn(
            ImportedConfig::notImported(),
        );
        $this->assetsHandler->expects($this->once())->method('roadRunnerBinaryExistsInPath')->willReturn($rrBinExists);
        $this->assetsHandler->expects($this->once())->method('importShlinkAssetsFromPath');

        $this->configWriter->expects($this->once())->method('toFile')->with($this->anything(), $this->isArray());

        $this->installationRunner->runUpdate($this->createStub(SymfonyStyle::class), $this->initCommand);
    }

    public static function provideCommands(): iterable
    {
        yield 'update with no rr binary' => [
            false,
            '--skip-initialize-db --clear-db-cache',
        ];
        yield 'update with rr binary' => [
            true,
            '--skip-initialize-db --clear-db-cache --download-rr-binary',
        ];
    }
}
