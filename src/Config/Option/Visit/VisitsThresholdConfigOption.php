<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Visit;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\ConfigOptionInterface;
use Shlinkio\Shlink\Installer\Config\Option\DependentConfigOptionInterface;
use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidatorsTrait;
use Symfony\Component\Console\Style\StyleInterface;

class VisitsThresholdConfigOption implements ConfigOptionInterface, DependentConfigOptionInterface
{
    use ConfigOptionsValidatorsTrait;

    public function getConfigPath(): array
    {
        return ['delete_short_urls', 'visits_threshold'];
    }

    public function shouldBeAsked(PathCollection $currentOptions): bool
    {
        $shouldCheckVisits = $currentOptions->getValueInPath(CheckVisitsThresholdConfigOption::CONFIG_PATH);
        return $shouldCheckVisits && ! $currentOptions->pathExists($this->getConfigPath());
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): int
    {
        return $io->ask(
            'What is the amount of visits from which the system will not allow short URLs to be deleted?',
            '15',
            [$this, 'validatePositiveNumber'],
        );
    }

    public function getDependentOption(): string
    {
        return CheckVisitsThresholdConfigOption::class;
    }
}
