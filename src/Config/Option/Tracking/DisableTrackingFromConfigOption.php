<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Tracking;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Symfony\Component\Console\Style\StyleInterface;

class DisableTrackingFromConfigOption extends AbstractDisableTrackingDependentConfigOption
{
    public function getDeprecatedPath(): array
    {
        return ['tracking', 'disable_tracking_from'];
    }

    public function getEnvVar(): string
    {
        return 'DISABLE_TRACKING_FROM';
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): ?string
    {
        return $io->ask(
            'Provide a comma-separated list of IP addresses, CIDR blocks or wildcard addresses (1.2.*.*) from '
            . 'which you want tracking to be disabled',
        );
    }
}
