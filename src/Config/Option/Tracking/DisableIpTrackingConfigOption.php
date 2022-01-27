<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Tracking;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Symfony\Component\Console\Style\StyleInterface;

class DisableIpTrackingConfigOption extends AbstractDisableTrackingDependentConfigOption
{
    public const ENV_VAR = 'DISABLE_IP_TRACKING';
    public const CONFIG_PATH = [self::ENV_VAR];

    public function getDeprecatedPath(): array
    {
        return ['tracking', 'disable_ip_tracking'];
    }

    public function getEnvVar(): string
    {
        return self::ENV_VAR;
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): bool
    {
        return $io->confirm('Do you want to disable tracking of visitors\' IP addresses?', false);
    }
}
