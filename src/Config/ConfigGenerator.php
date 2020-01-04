<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config;

use Shlinkio\Shlink\Installer\Config\Option\ConfigOptionInterface;
use Shlinkio\Shlink\Installer\Util\PathCollection;
use Symfony\Component\Console\Style\SymfonyStyle;

class ConfigGenerator implements ConfigGeneratorInterface
{
    /** @var ConfigOptionsManagerInterface */
    private $configOptionsManager;
    /** @var array */
    private $configOptionsGroups;

    public function __construct(ConfigOptionsManagerInterface $configOptionsManager, array $configOptionsGroups)
    {
        $this->configOptionsManager = $configOptionsManager;
        $this->configOptionsGroups = $configOptionsGroups;
    }

    public function generateConfigInteractively(SymfonyStyle $io, array $previousConfig): array
    {
        // TODO Filter config options, based on those which answer is present in current config,
        // TODO Sort config options, based on which they depend on

        $answers = new PathCollection($previousConfig);

        foreach ($this->configOptionsGroups as $title => $configOptions) {
            foreach ($configOptions as $index => $configOption) {
                /** @var ConfigOptionInterface $plugin */
                $plugin = $this->configOptionsManager->get($configOption);
                if (! $plugin->shouldBeAsked($answers)) {
                    unset($this->configOptionsGroups[$title][$index]);
                } else {
                    $this->configOptionsGroups[$title][$index] = $plugin;
                }
            }

            if (empty($this->configOptionsGroups[$title])) {
                unset($this->configOptionsGroups[$title]);
            }
        }

        foreach ($this->configOptionsGroups as $title => $configOptions) {
            $io->title($title);

            /** @var ConfigOptionInterface $plugin */
            foreach ($configOptions as $plugin) {
                if (! $plugin->shouldBeAsked($answers)) { // FIXME We are checking this twice...
                    continue;
                }

                $answer = $plugin->ask($io, $answers);
                $answers->setValueInPath($answer, $plugin->getConfigPath());
            }
        }

        return $answers->toArray();
    }
}
