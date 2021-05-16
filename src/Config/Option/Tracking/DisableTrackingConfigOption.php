<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Tracking;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class DisableTrackingConfigOption extends BaseConfigOption
{
    public const CONFIG_PATH = ['tracking', 'disable_tracking'];

    public function getConfigPath(): array
    {
        return self::CONFIG_PATH;
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): bool
    {
        return $io->confirm('Do you want to completely disable visits tracking?', false);
    }
}
