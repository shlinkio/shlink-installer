<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config;

use Shlinkio\Shlink\Installer\Config\Option\ConfigOptionInterface;
use Shlinkio\Shlink\Installer\Config\Option\DependentConfigOptionInterface;
use Shlinkio\Shlink\Installer\Util\PathCollection;
use Symfony\Component\Console\Style\SymfonyStyle;

use function Functional\contains;

class ConfigGenerator implements ConfigGeneratorInterface
{
    /** @var ConfigOptionsManagerInterface */
    private $configOptionsManager;
    /** @var array */
    private $configOptionsGroups;
    /** @var array|null */
    private $enabledOptions;

    public function __construct(
        ConfigOptionsManagerInterface $configOptionsManager,
        array $configOptionsGroups,
        ?array $enabledOptions
    ) {
        $this->configOptionsManager = $configOptionsManager;
        $this->configOptionsGroups = $configOptionsGroups;
        $this->enabledOptions = $enabledOptions;
    }

    public function generateConfigInteractively(SymfonyStyle $io, array $previousConfig): PathCollection
    {
        // TODO Sort config options, based on which they depend on

        $answers = new PathCollection($previousConfig);
        $alreadyRenderedTitles = [];

        // FIXME Improve code quality on these nested loops
        foreach ($this->configOptionsGroups as $title => $configOptions) {
            foreach ($configOptions as $configOption) {
                if ($this->enabledOptions !== null && ! contains($this->enabledOptions, $configOption)) {
                    continue;
                }

                /** @var ConfigOptionInterface $plugin */
                $plugin = $this->configOptionsManager->get($configOption);
                $dependantPlugin = $plugin instanceof DependentConfigOptionInterface
                    ? $this->configOptionsManager->get($plugin->getDependentOption())
                    : null;

                if (! $plugin->shouldBeAsked($answers, $dependantPlugin)) {
                    continue;
                }

                // Render every title only once, and only as soon as we find a plugin that needs to be asked
                if (! contains($alreadyRenderedTitles, $title)) {
                    $alreadyRenderedTitles[] = $title;
                    $io->title($title);
                }

                $answer = $plugin->ask($io, $answers, $dependantPlugin);
                $answers->setValueInPath($answer, $plugin->getConfigPath());
            }
        }

        return $answers;
    }
}
