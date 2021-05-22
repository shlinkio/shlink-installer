<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Tracking;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\AbstractWithDeprecatedPathConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class DisableTrackParamConfigOption extends AbstractWithDeprecatedPathConfigOption
{
    public function getConfigPath(): array
    {
        return ['tracking', 'disable_track_param'];
    }

    protected function getDeprecatedPath(): array
    {
        return ['app_options', 'disable_track_param'];
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): ?string
    {
        return $io->ask(
            'Provide a parameter name that you will be able to use to disable tracking on specific request to '
            . 'short URLs (leave empty and this feature won\'t be enabled)',
        );
    }
}
