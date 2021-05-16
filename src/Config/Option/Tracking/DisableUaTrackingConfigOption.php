<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Tracking;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Symfony\Component\Console\Style\StyleInterface;

class DisableUaTrackingConfigOption extends AbstractDisableTrackingDependentConfigOption
{
    public function getConfigPath(): array
    {
        return ['tracking', 'disable_ua_tracking'];
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): bool
    {
        return $io->confirm('Do you want to disable tracking of visitors\' "User Agents"?', false);
    }
}
