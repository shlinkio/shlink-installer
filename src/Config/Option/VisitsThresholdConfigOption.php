<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidatorsTrait;
use Shlinkio\Shlink\Installer\Util\PathCollection;
use Symfony\Component\Console\Style\SymfonyStyle;

class VisitsThresholdConfigOption implements ConfigOptionInterface, DependentConfigOptionInterface
{
    use ConfigOptionsValidatorsTrait;

    public function getConfigPath(): array
    {
        return ['delete_short_urls', 'visits_threshold'];
    }

    public function shouldBeAsked(PathCollection $currentOptions, ?ConfigOptionInterface $dependentOption): bool
    {
        $shouldCheckVisits = $dependentOption !== null
            ? $currentOptions->getValueInPath($dependentOption->getConfigPath())
            : true;
        return $shouldCheckVisits && ! $currentOptions->pathExists($this->getConfigPath());
    }

    public function ask(SymfonyStyle $io, PathCollection $currentOptions, ?ConfigOptionInterface $dependentOption)
    {
        return $io->ask(
            'What is the amount of visits from which the system will not allow short URLs to be deleted?',
            '15',
            [$this, 'validatePositiveNumber']
        );
    }

    public function getDependentOption(): string
    {
        return CheckVisitsThresholdConfigOption::class;
    }
}
