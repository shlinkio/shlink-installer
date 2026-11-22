<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Tracking;

use Override;
use Symfony\Component\Console\Style\StyleInterface;

class DisableTrackParamConfigOption extends AbstractDisableTrackingDependentConfigOption
{
    #[Override]
    public function getEnvVar(): string
    {
        return 'DISABLE_TRACK_PARAM';
    }

    #[Override]
    public function ask(StyleInterface $io, array $currentOptions): string|null
    {
        return $io->ask(
            'Provide a parameter name that you will be able to use to disable tracking on specific request to '
            . 'short URLs (leave empty and this feature won\'t be enabled)',
        );
    }
}
