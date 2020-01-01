<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Symfony\Component\Console\Style\SymfonyStyle;

class CheckVisitsThresholdConfigOption extends BaseConfigOption
{
    public function getConfigPath(): array
    {
        return ['delete_short_urls', 'check_visits_threshold'];
    }

    public function ask(SymfonyStyle $io, array $currentOptions)
    {
        return $io->confirm(
            'Do you want to enable a safety check which will not allow short URLs to be deleted after receiving '
            . 'a specific amount of visits?'
        );
    }
}
