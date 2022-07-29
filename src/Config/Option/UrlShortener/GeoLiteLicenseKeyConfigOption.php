<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\UrlShortener;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class GeoLiteLicenseKeyConfigOption extends BaseConfigOption
{
    public function getEnvVar(): string
    {
        return 'GEOLITE_LICENSE_KEY';
    }

    public function ask(StyleInterface $io, array $currentOptions): ?string
    {
        return $io->ask(
            'Provide a GeoLite2 license key. Leave empty to disable geolocation. '
            . '(Go to https://shlink.io/documentation/geolite-license-key to know how to generate it)',
        );
    }
}
