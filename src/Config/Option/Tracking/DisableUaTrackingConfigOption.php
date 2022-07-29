<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Tracking;

use Symfony\Component\Console\Style\StyleInterface;

class DisableUaTrackingConfigOption extends AbstractDisableTrackingDependentConfigOption
{
    public function getEnvVar(): string
    {
        return 'DISABLE_UA_TRACKING';
    }

    public function ask(StyleInterface $io, array $currentOptions): bool
    {
        return $io->confirm('Do you want to disable tracking of visitors\' "User Agents"?', false);
    }
}
