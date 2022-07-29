<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Tracking;

use Symfony\Component\Console\Style\StyleInterface;

class OrphanVisitsTrackingConfigOption extends AbstractDisableTrackingDependentConfigOption
{
    public function getEnvVar(): string
    {
        return 'TRACK_ORPHAN_VISITS';
    }

    public function ask(StyleInterface $io, array $currentOptions): bool
    {
        return $io->confirm(
            'Do you want track orphan visits? (visits to the base URL, invalid short URLs or other "not found" URLs)',
            true,
        );
    }
}
