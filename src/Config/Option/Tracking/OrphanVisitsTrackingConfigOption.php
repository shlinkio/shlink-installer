<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Tracking;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class OrphanVisitsTrackingConfigOption extends BaseConfigOption
{
    public function getEnvVar(): string
    {
        return 'TRACK_ORPHAN_VISITS';
    }

    public function getDeprecatedPath(): array
    {
        return ['tracking', 'track_orphan_visits'];
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): bool
    {
        return $io->confirm(
            'Do you want track orphan visits? (visits to the base URL, invalid short URLs or other "not found" URLs)',
            true,
        );
    }
}
