<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\ConfigGenerator;
use Shlinkio\Shlink\Installer\Config\ConfigOptionsManagerInterface;
use Shlinkio\Shlink\Installer\Config\Option\ConfigOptionInterface;
use Shlinkio\Shlink\Installer\Config\Option\ConfigOptionMigratorInterface;
use Shlinkio\Shlink\Installer\Config\Option\DependentConfigOptionInterface;
use Shlinkio\Shlink\Installer\Util\ArrayUtils;
use Symfony\Component\Console\Style\StyleInterface;

use function count;
use function get_class;

class ConfigGeneratorTest extends TestCase
{
    private MockObject & ConfigOptionsManagerInterface $configOptionsManager;
    private MockObject & StyleInterface $io;

    public function setUp(): void
    {
        $this->configOptionsManager = $this->createMock(ConfigOptionsManagerInterface::class);
        $this->io = $this->createMock(StyleInterface::class);
    }

    #[Test, DataProvider('provideConfigOptions')]
    public function configuresExpectedPlugins(
        array $configOptionsGroups,
        array|null $enabledOptions,
        int $expectedPrintTitleCalls,
    ): void {
        $totalPlugins = count(ArrayUtils::flatten($configOptionsGroups));
        $expectedQuestions = $enabledOptions === null ? $totalPlugins : count($enabledOptions);

        $plugin = $this->createMock(ConfigOptionInterface::class);
        $plugin->expects($this->exactly($expectedQuestions))->method('shouldBeAsked')->willReturn(true);
        $plugin->expects($this->exactly($expectedQuestions))->method('getEnvVar')->willReturn('ENV_VAR');
        $plugin->expects($this->exactly($expectedQuestions))->method('ask')->willReturn('value');
        $this->configOptionsManager->expects($this->exactly($expectedQuestions))->method('get')->willReturn($plugin);
        $this->io->expects($this->exactly($expectedPrintTitleCalls))->method('title');

        $generator = new ConfigGenerator($this->configOptionsManager, $configOptionsGroups, $enabledOptions);
        $generator->generateConfigInteractively($this->io, []);
    }

    public static function provideConfigOptions(): iterable
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

    #[Test, AllowMockObjectsWithoutExpectations]
    public function pluginsAreAskedInProperOrder(): void
    {
        $orderedAskedOptions = [];
        $regularPlugin = new class ($orderedAskedOptions) implements ConfigOptionInterface {
            private array $orderedAskedOptions; // @phpstan-ignore-line

            public function __construct(array &$orderedAskedOptions)
            {
                $this->orderedAskedOptions = &$orderedAskedOptions;
            }

            public function getEnvVar(): string
            {
                return '';
            }

            public function shouldBeAsked(array $currentOptions): bool
            {
                return true;
            }

            public function ask(StyleInterface $io, array $currentOptions): string
            {
                $this->orderedAskedOptions[] = 'a';
                return 'value';
            }
        };
        $dependentPlugin = new class ($orderedAskedOptions, get_class($regularPlugin)) implements
            ConfigOptionInterface,
            DependentConfigOptionInterface
        {
            private array $orderedAskedOptions; // @phpstan-ignore-line

            public function __construct(array &$orderedAskedOptions, private string $regularPluginClass)
            {
                $this->orderedAskedOptions = &$orderedAskedOptions;
            }

            public function getEnvVar(): string
            {
                return '';
            }

            public function shouldBeAsked(array $currentOptions): bool
            {
                return true;
            }

            public function ask(StyleInterface $io, array $currentOptions): string
            {
                $this->orderedAskedOptions[] = 'depends_on_a';
                return '';
            }

            public function getDependentOption(): string
            {
                return $this->regularPluginClass;
            }
        };

        $this->configOptionsManager->expects($this->exactly(2))->method('get')->willReturnCallback(
            fn (string $configOption) => $configOption === 'a' ? $regularPlugin : $dependentPlugin,
        );

        $optionsGroups = [
            'group_a' => ['depends_on_a', 'a'],
        ];
        $generator = new ConfigGenerator($this->configOptionsManager, $optionsGroups, null);
        $generator->generateConfigInteractively($this->io, []);

        self::assertEquals(['a', 'depends_on_a'], $orderedAskedOptions);
    }

    #[Test, DataProvider('provideMigratorValues'), AllowMockObjectsWithoutExpectations]
    public function migratorPluginsAreProcessedWhenTheValuesShouldNotBeAsked(
        array $oldConfig,
        array $expectedResult,
    ): void {
        $regularPlugin = new class implements ConfigOptionInterface, ConfigOptionMigratorInterface {
            public function getEnvVar(): string
            {
                return 'env_var';
            }

            public function shouldBeAsked(array $currentOptions): bool
            {
                return false;
            }

            public function ask(StyleInterface $io, array $currentOptions): string
            {
                return 'value';
            }

            public function tryToMigrateValue(mixed $currentValue): mixed
            {
                return $currentValue === 'foo' ? 'migrated_value' : $currentValue;
            }
        };

        $this->configOptionsManager->expects($this->once())->method('get')->willReturn($regularPlugin);

        $optionsGroups = ['group_a' => ['a']];
        $generator = new ConfigGenerator($this->configOptionsManager, $optionsGroups, null);
        $result = $generator->generateConfigInteractively($this->io, $oldConfig);

        self::assertEquals($expectedResult, $result);
    }

    public static function provideMigratorValues(): iterable
    {
        yield [['env_var' => 'old_value'], ['env_var' => 'old_value']];
        yield [['env_var' => 'foo'], ['env_var' => 'migrated_value']];
        yield [[], []];
    }
}
