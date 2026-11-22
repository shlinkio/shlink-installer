<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Tracking;

use Override;
use Symfony\Component\Console\Style\StyleInterface;

class OrphanVisitsTrackingConfigOption extends AbstractDisableTrackingDependentConfigOption
{
    #[Override]
    public function getEnvVar(): string
    {
        return 'TRACK_ORPHAN_VISITS';
    }

    #[Override]
    public function ask(StyleInterface $io, array $currentOptions): bool
    {
        return $io->confirm(
            'Do you want track orphan visits? (visits to the base URL, invalid short URLs or other "not found" URLs)',
            true,
        );
    }
}
