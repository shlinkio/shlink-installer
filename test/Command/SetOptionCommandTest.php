<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Command;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Command\SetOptionCommand;
use Shlinkio\Shlink\Installer\Config\ConfigOptionsManagerInterface;
use Shlinkio\Shlink\Installer\Config\Option\ConfigOptionInterface;
use Shlinkio\Shlink\Installer\Exception\InvalidShlinkPathException;
use Shlinkio\Shlink\Installer\Service\ShlinkAssetsHandlerInterface;
use Shlinkio\Shlink\Installer\Util\ConfigWriterInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

use function chdir;
use function getcwd;

class SetOptionCommandTest extends TestCase
{
    private CommandTester $commandTester;
    private MockObject & ConfigWriterInterface $configWriter;
    private MockObject & ShlinkAssetsHandlerInterface $assetsHandler;
    private MockObject & ConfigOptionsManagerInterface $optionsManager;
    private MockObject & Filesystem $filesystem;
    private string $initialCwd;

    public function setUp(): void
    {
        $this->initialCwd = getcwd() ?: '';
        chdir(__DIR__ . '/../../test-resources');

        $this->configWriter = $this->createMock(ConfigWriterInterface::class);
        $this->assetsHandler = $this->createMock(ShlinkAssetsHandlerInterface::class);
        $this->optionsManager = $this->createMock(ConfigOptionsManagerInterface::class);
        $this->filesystem = $this->createMock(Filesystem::class);

        $app = new Application();
        $command = new SetOptionCommand(
            $this->configWriter,
            $this->assetsHandler,
            $this->optionsManager,
            $this->filesystem,
            ['foo' => [
                'Set option 1' => 'option_1',
                'Set option 2' => 'option_2',
                'Set option 3' => 'option_3',
            ]],
            ['option_1', 'option_3'],
        );
        $app->add($command);
        $this->commandTester = new CommandTester($command);
    }

    protected function tearDown(): void
    {
        chdir($this->initialCwd);
    }

    #[Test]
    public function exceptionIsThrownWhenGeneratedConfigFileDoesNotExist(): void
    {
        $this->filesystem->expects($this->once())->method('exists')->with($this->isType('string'))->willReturn(false);
        $this->configWriter->expects($this->never())->method('toFile');
        $this->assetsHandler->expects($this->never())->method('dropCachedConfigIfAny');
        $this->optionsManager->expects($this->never())->method('get');
        $this->expectException(InvalidShlinkPathException::class);

        $this->commandTester->execute([]);
    }

    #[Test]
    public function expectedOptionsAreOfferedBasedOnConfig(): void
    {
        $this->filesystem->expects($this->once())->method('exists')->with($this->isType('string'))->willReturn(true);
        $this->configWriter->expects($this->once())->method('toFile');
        $this->assetsHandler->expects($this->once())->method('dropCachedConfigIfAny')->with(
            $this->isInstanceOf(SymfonyStyle::class),
        );

        $plugin = $this->createMock(ConfigOptionInterface::class);
        $plugin->expects($this->once())->method('ask')->willReturn('');
        $plugin->expects($this->once())->method('getEnvVar')->willReturn('foo');
        $this->optionsManager->expects($this->once())->method('get')->with($this->isType('string'))->willReturn(
            $plugin,
        );

        $this->commandTester->setInputs([1]);
        $this->commandTester->execute([]);
        $output = $this->commandTester->getDisplay();

        self::assertEquals(0, $this->commandTester->getStatusCode());
        self::assertStringContainsString('Set option 1', $output);
        self::assertStringContainsString('Set option 3', $output);
        self::assertStringNotContainsString('Set option 2', $output);
    }
}
