<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config;

use Shlinkio\Shlink\Installer\Config\Option\ConfigOptionInterface;
use Shlinkio\Shlink\Installer\Config\Option\ConfigOptionMigratorInterface;
use Shlinkio\Shlink\Installer\Config\Option\DependentConfigOptionInterface;
use Shlinkio\Shlink\Installer\Util\ArrayUtils;
use Symfony\Component\Console\Style\StyleInterface;

use function array_combine;
use function array_filter;
use function array_map;
use function usort;

class ConfigGenerator implements ConfigGeneratorInterface
{
    public function __construct(
        private readonly ConfigOptionsManagerInterface $configOptionsManager,
        private readonly array $configOptionsGroups,
        private readonly array|null $enabledOptions,
    ) {
    }

    public function generateConfigInteractively(StyleInterface $io, array $previousConfig): array
    {
        $pluginsGroups = $this->resolveAndSortOptions();
        $answers = $previousConfig;
        $alreadyRenderedTitles = [];

        // FIXME Improve code quality on these nested loops
        foreach ($pluginsGroups as $title => $configOptions) {
            foreach ($configOptions as $configOption => $plugin) {
                $optionIsEnabled = $this->enabledOptions === null || ArrayUtils::contains(
                    $configOption,
                    $this->enabledOptions,
                );
                $shouldAsk = $optionIsEnabled && $plugin->shouldBeAsked($answers);
                if (! $shouldAsk) {
                    if ($plugin instanceof ConfigOptionMigratorInterface && isset($answers[$plugin->getEnvVar()])) {
                        $answers[$plugin->getEnvVar()] = $plugin->tryToMigrateValue($answers[$plugin->getEnvVar()]);
                    }

                    continue;
                }

                // Render every title only once, and only as soon as we find a plugin that should be asked
                if (! ArrayUtils::contains($title, $alreadyRenderedTitles)) {
                    $alreadyRenderedTitles[] = $title;
                    $io->title($title);
                }

                $answers[$plugin->getEnvVar()] = $plugin->ask($io, $answers);
            }
        }

        return $answers;
    }

    /**
     * @return ConfigOptionInterface[][]
     */
    private function resolveAndSortOptions(): array
    {
        $resolveAndSortPlugins = function (array $configOptions) {
            $plugins = array_map(
                fn (string $configOption) => $this->configOptionsManager->get($configOption),
                $configOptions,
            );

            // Sort plugins based on which other plugins they depend on
            usort(
                $plugins,
                static fn (ConfigOptionInterface $a, ConfigOptionInterface $b): int =>
                    $a instanceof DependentConfigOptionInterface && $a->getDependentOption() === $b::class ? 1 : 0,
            );

            return array_combine($configOptions, $plugins);
        };
        $filterDisabledOptions = fn (array $configOptions) => array_filter(
            $configOptions,
            fn (string $option) => $this->enabledOptions === null || ArrayUtils::contains(
                $option,
                $this->enabledOptions,
            ),
        );

        return array_map(
            static fn (array $configOptions) => $resolveAndSortPlugins($filterDisabledOptions($configOptions)),
            $this->configOptionsGroups,
        );
    }
}
