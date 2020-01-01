<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidatorsTrait;
use Symfony\Component\Console\Style\SymfonyStyle;

class VisitsThresholdConfigOption implements ConfigOptionInterface, DependentConfigOptionInterface
{
    use ConfigOptionsValidatorsTrait;

    public function getConfigPath(): array
    {
        return ['delete_short_urls', 'visits_threshold'];
    }

    public function shouldBeAsked(array $currentOptions): bool
    {
        $shouldCheckVisits = $currentOptions[CheckVisitsThresholdConfigOption::class] ?? null;
        return $shouldCheckVisits === true && ! isset($currentOptions[self::class]);
    }

    public function ask(SymfonyStyle $io, array $currentOptions)
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
