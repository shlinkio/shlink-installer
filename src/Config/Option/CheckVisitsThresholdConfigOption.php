<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Shlinkio\Shlink\Installer\Util\PathCollection;
use Symfony\Component\Console\Style\StyleInterface;

class CheckVisitsThresholdConfigOption extends BaseConfigOption
{
    public const CONFIG_PATH = ['delete_short_urls', 'check_visits_threshold'];

    public function getConfigPath(): array
    {
        return self::CONFIG_PATH;
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): bool
    {
        return $io->confirm(
            'Do you want to enable a safety check which will not allow short URLs to be deleted after receiving '
            . 'a specific amount of visits?',
        );
    }
}
