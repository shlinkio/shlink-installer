<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Shlinkio\Shlink\Installer\Config\ConfigGenerator;
use Shlinkio\Shlink\Installer\Config\ConfigOptionsManagerInterface;
use Shlinkio\Shlink\Installer\Config\Option\ConfigOptionInterface;
use Shlinkio\Shlink\Installer\Config\Option\DependentConfigOptionInterface;
use Shlinkio\Shlink\Installer\Util\PathCollection;
use Symfony\Component\Console\Style\StyleInterface;

use function count;
use function Functional\flatten;
use function get_class;

class ConfigGeneratorTest extends TestCase
{
    /** @var ObjectProphecy */
    private $configOptionsManager;
    /** @var ObjectProphecy */
    private $plugin;
    /** @var ObjectProphecy */
    private $io;

    public function setUp(): void
    {
        $this->configOptionsManager = $this->prophesize(ConfigOptionsManagerInterface::class);
        $this->plugin = $this->prophesize(ConfigOptionInterface::class);
        $this->plugin->shouldBeAsked(Argument::cetera())->willReturn(true);
        $this->plugin->getConfigPath()->willReturn(['path']);
        $this->plugin->ask(Argument::cetera())->willReturn('value');
        $this->io = $this->prophesize(StyleInterface::class);
    }

    /**
     * @test
     * @dataProvider provideConfigOptions
     */
    public function configuresExpectedPlugins(
        array $configOptionsGroups,
        ?array $enabledOptions,
        int $expectedPrintTitleCalls
    ): void {
        $totalPlugins = count(flatten($configOptionsGroups));
        $expectedQuestions = $enabledOptions === null ? $totalPlugins : count($enabledOptions);

        $pluginShouldBeAsked = $this->plugin->shouldBeAsked(Argument::cetera())->willReturn(true);
        $getPath = $this->plugin->getConfigPath()->willReturn(['path']);
        $ask = $this->plugin->ask(Argument::cetera())->willReturn('value');
        $getPlugin = $this->configOptionsManager->get(Argument::any())->willReturn($this->plugin->reveal());
        $printTitle = $this->io->title(Argument::any());

        $generator = new ConfigGenerator($this->configOptionsManager->reveal(), $configOptionsGroups, $enabledOptions);
        $generator->generateConfigInteractively($this->io->reveal(), []);

        $pluginShouldBeAsked->shouldHaveBeenCalledTimes($expectedQuestions);
        $ask->shouldHaveBeenCalledTimes($expectedQuestions);
        $getPath->shouldHaveBeenCalledTimes($expectedQuestions);
        $getPlugin->shouldHaveBeenCalledTimes($totalPlugins);
        $printTitle->shouldHaveBeenCalledTimes($expectedPrintTitleCalls);
    }

    public function provideConfigOptions(): iterable
    {
        $optionsGroups = [
            'group_a' => ['some', 'thing'],
            'group_b' => ['foo', 'bar', 'baz'],
            'group_c' => ['b', 'a'],
        ];

        yield 'none disabled' => [$optionsGroups, null, 3];
        yield 'all disabled' => [$optionsGroups, [], 0];
        yield '3 enabled' => [$optionsGroups, ['some', 'baz', 'a'], 3];
        yield '4 enabled' => [$optionsGroups, ['some', 'baz', 'a', 'b'], 3];
        yield '2 enabled' => [$optionsGroups, ['foo', 'a'], 2];
        yield '1 enabled' => [$optionsGroups, ['foo'], 1];
    }

    /** @test */
    public function pluginsAreAskedInProperOrder(): void
    {
        $orderedAskedOptions = [];
        $regularPlugin = $this->plugin->reveal();
        $regularPluginClass = get_class($regularPlugin);
        $dependentPlugin = new class ($orderedAskedOptions, $regularPluginClass) implements
            ConfigOptionInterface,
            DependentConfigOptionInterface
        {
            /** @var array */
            private $orderedAskedOptions;
            /** @var string */
            private $regularPluginClass;

            public function __construct(array &$orderedAskedOptions, string $regularPluginClass)
            {
                $this->orderedAskedOptions = &$orderedAskedOptions;
                $this->regularPluginClass = $regularPluginClass;
            }

            public function getConfigPath(): array
            {
                return [];
            }

            public function shouldBeAsked(PathCollection $currentOptions): bool
            {
                return true;
            }

            public function ask(StyleInterface $io, PathCollection $currentOptions): string
            {
                $this->orderedAskedOptions[] = 'depends_on_a';
                return '';
            }

            public function getDependentOption(): string
            {
                return $this->regularPluginClass;
            }
        };
        $this->plugin->ask(Argument::cetera())->will(function () use (&$orderedAskedOptions) {
            $orderedAskedOptions[] = 'a';
            return 'value';
        });

        $getPlugin = $this->configOptionsManager->get(Argument::any())->will(
            function (array $args) use ($regularPlugin, $dependentPlugin) {
                [$configOption] = $args;
                return $configOption === 'a' ? $regularPlugin : $dependentPlugin;
            }
        );

        $optionsGroups = [
            'group_a' => ['depends_on_a', 'a'],
        ];
        $generator = new ConfigGenerator($this->configOptionsManager->reveal(), $optionsGroups, null);
        $generator->generateConfigInteractively($this->io->reveal(), []);

        $getPlugin->shouldHaveBeenCalledTimes(2);
        $this->assertEquals(['a', 'depends_on_a'], $orderedAskedOptions);
    }
}
