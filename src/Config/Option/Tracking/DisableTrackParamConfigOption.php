<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Tracking;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Symfony\Component\Console\Style\StyleInterface;

class DisableTrackParamConfigOption extends AbstractDisableTrackingDependentConfigOption
{
    public function getEnvVar(): string
    {
        return 'DISABLE_TRACK_PARAM';
    }

    public function getDeprecatedPath(): array
    {
        return ['tracking', 'disable_track_param'];
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): ?string
    {
        return $io->ask(
            'Provide a parameter name that you will be able to use to disable tracking on specific request to '
            . 'short URLs (leave empty and this feature won\'t be enabled)',
        );
    }
}
