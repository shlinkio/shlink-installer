<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Tracking;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Symfony\Component\Console\Style\StyleInterface;

class DisableIpTrackingConfigOption extends AbstractDisableTrackingDependentConfigOption
{
    public const CONFIG_PATH = ['tracking', 'disable_ip_tracking'];

    public function getConfigPath(): array
    {
        return self::CONFIG_PATH;
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): bool
    {
        return $io->confirm('Do you want to disable tracking of visitors\' IP addresses?', false);
    }
}
