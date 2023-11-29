<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config;

use Shlinkio\Shlink\Installer\Config\Option\ConfigOptionInterface;
use Shlinkio\Shlink\Installer\Config\Option\ConfigOptionMigratorInterface;
use Shlinkio\Shlink\Installer\Config\Option\DependentConfigOptionInterface;
use Symfony\Component\Console\Style\StyleInterface;

use function array_combine;
use function array_map;
use function Functional\compose;
use function Functional\contains;
use function Functional\select;
use function Functional\sort;
use function get_class;

class ConfigGenerator implements ConfigGeneratorInterface
{
    public function __construct(
        private readonly ConfigOptionsManagerInterface $configOptionsManager,
        private readonly array $configOptionsGroups,
        private readonly ?array $enabledOptions,
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
                $optionIsEnabled = $this->enabledOptions === null || contains($this->enabledOptions, $configOption);
                $shouldAsk = $optionIsEnabled && $plugin->shouldBeAsked($answers);
                if (! $shouldAsk) {
                    if ($plugin instanceof ConfigOptionMigratorInterface && isset($answers[$plugin->getEnvVar()])) {
                        $answers[$plugin->getEnvVar()] = $plugin->tryToMigrateValue($answers[$plugin->getEnvVar()]);
                    }

                    continue;
                }

                // Render every title only once, and only as soon as we find a plugin that should be asked
                if (! contains($alreadyRenderedTitles, $title)) {
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
        // Sort plugins based on which other plugins they depend on
        $dependentPluginSorter = static fn (ConfigOptionInterface $a, ConfigOptionInterface $b): int =>
            $a instanceof DependentConfigOptionInterface && $a->getDependentOption() === get_class($b) ? 1 : 0;
        $sortAndResolvePlugins = fn (array $configOptions) => array_combine(
            $configOptions,
            sort(
                array_map(
                    fn (string $configOption) => $this->configOptionsManager->get($configOption),
                    $configOptions,
                ),
                $dependentPluginSorter,
            ),
        );
        $filterDisabledOptions = fn (array $configOptions) => select(
            $configOptions,
            fn (string $option) => $this->enabledOptions === null || contains($this->enabledOptions, $option),
        );

        return array_map(compose($filterDisabledOptions, $sortAndResolvePlugins), $this->configOptionsGroups);
    }
}
