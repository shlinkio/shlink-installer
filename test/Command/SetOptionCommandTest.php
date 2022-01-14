<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Command;

use Laminas\Config\Writer\WriterInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Shlinkio\Shlink\Installer\Command\SetOptionCommand;
use Shlinkio\Shlink\Installer\Config\ConfigOptionsManagerInterface;
use Shlinkio\Shlink\Installer\Config\Option\ConfigOptionInterface;
use Shlinkio\Shlink\Installer\Exception\InvalidShlinkPathException;
use Shlinkio\Shlink\Installer\Service\ShlinkAssetsHandlerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

use function chdir;
use function getcwd;

class SetOptionCommandTest extends TestCase
{
    use ProphecyTrait;

    private CommandTester $commandTester;
    private ObjectProphecy $configWriter;
    private ObjectProphecy $assetsHandler;
    private ObjectProphecy $optionsManager;
    private ObjectProphecy $filesystem;
    private string $initialCwd;

    public function setUp(): void
    {
        $this->initialCwd = getcwd();
        chdir(__DIR__ . '/../../test-resources');

        $this->configWriter = $this->prophesize(WriterInterface::class);
        $this->assetsHandler = $this->prophesize(ShlinkAssetsHandlerInterface::class);
        $this->optionsManager = $this->prophesize(ConfigOptionsManagerInterface::class);
        $this->filesystem = $this->prophesize(Filesystem::class);

        $app = new Application();
        $command = new SetOptionCommand(
            $this->configWriter->reveal(),
            $this->assetsHandler->reveal(),
            $this->optionsManager->reveal(),
            $this->filesystem->reveal(),
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

    /** @test */
    public function exceptionIsThrownWhenGeneratedConfigFileDoesNotExist(): void
    {
        $exists = $this->filesystem->exists(Argument::type('string'))->willReturn(false);
        $toFile = $this->configWriter->toFile(Argument::cetera());
        $dropCachedConfig = $this->assetsHandler->dropCachedConfigIfAny(Argument::type(SymfonyStyle::class));

        $plugin = $this->prophesize(ConfigOptionInterface::class);
        $ask = $plugin->ask(Argument::cetera())->willReturn('');
        $getEnvVar = $plugin->getEnvVar()->willReturn('foo');
        $getPlugin = $this->optionsManager->get(Argument::type('string'))->willReturn($plugin->reveal());

        $exists->shouldBeCalledOnce();
        $toFile->shouldNotBeCalled();
        $dropCachedConfig->shouldNotBeCalled();
        $ask->shouldNotBeCalled();
        $getEnvVar->shouldNotBeCalled();
        $getPlugin->shouldNotBeCalled();
        $this->expectException(InvalidShlinkPathException::class);

        $this->commandTester->execute([]);
    }

    /** @test */
    public function expectedOptionsAreOfferedBasedOnConfig(): void
    {
        $exists = $this->filesystem->exists(Argument::type('string'))->willReturn(true);
        $toFile = $this->configWriter->toFile(Argument::cetera());
        $dropCachedConfig = $this->assetsHandler->dropCachedConfigIfAny(Argument::type(SymfonyStyle::class));

        $plugin = $this->prophesize(ConfigOptionInterface::class);
        $ask = $plugin->ask(Argument::cetera())->willReturn('');
        $getEnvVar = $plugin->getEnvVar()->willReturn('foo');
        $getPlugin = $this->optionsManager->get(Argument::type('string'))->willReturn($plugin->reveal());

        $this->commandTester->setInputs([1]);
        $this->commandTester->execute([]);
        $output = $this->commandTester->getDisplay();

        self::assertEquals(0, $this->commandTester->getStatusCode());
        self::assertStringContainsString('Set option 1', $output);
        self::assertStringContainsString('Set option 3', $output);
        self::assertStringNotContainsString('Set option 2', $output);
        $exists->shouldHaveBeenCalledOnce();
        $toFile->shouldHaveBeenCalledOnce();
        $dropCachedConfig->shouldHaveBeenCalledOnce();
        $ask->shouldHaveBeenCalledOnce();
        $getEnvVar->shouldHaveBeenCalledOnce();
        $getPlugin->shouldHaveBeenCalledOnce();
    }
}
