<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Symfony\Component\Console\Style\SymfonyStyle;

class DisableTrackParamConfigOption extends BaseConfigOption
{
    public function getConfigPath(): array
    {
        return ['app_options', 'disable_track_param'];
    }

    public function ask(SymfonyStyle $io, array $currentOptions)
    {
        return $io->ask(
            'Provide a parameter name that you will be able to use to disable tracking on specific request to '
            . 'short URLs (leave empty and this feature won\'t be enabled)'
        );
    }
}
