<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\UrlShortener;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class GeoLiteLicenseKeyConfigOption extends BaseConfigOption
{
    public function getConfigPath(): array
    {
        return ['geolite2', 'license_key'];
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): string
    {
        // TODO For Shlink 3.0, this option should be mandatory. The default value should be removed
        return $io->ask(
            'Provide a GeoLite2 license key. (Leave empty to use default one, but it is '
            . '<options=bold>strongly recommended</> to get your own. '
            . 'Go to https://shlink.io/documentation/geolite-license-key to know how to get it)',
        ) ?? 'G4Lm0C60yJsnkdPi';
    }
}
