<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\UrlShortener;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\DependentConfigOptionInterface;
use Shlinkio\Shlink\Installer\Config\Option\Geolocation\GeolocationStrategiesConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

use function is_string;
use function str_contains;

/**
 * @todo Move to Shlinkio\Shlink\Installer\Config\Option\Geolocation namespace in next major version
 */
class GeoLiteLicenseKeyConfigOption extends BaseConfigOption implements DependentConfigOptionInterface
{
    public function getEnvVar(): string
    {
        return 'GEOLITE_LICENSE_KEY';
    }

    public function ask(StyleInterface $io, array $currentOptions): string|null
    {
        // Skip this question if GeoLite2 was not one of the selected geolocation strategies
        $geolocationStrategies = $currentOptions[GeolocationStrategiesConfigOption::ENV_VAR] ?? '';
        if (is_string($geolocationStrategies) && !str_contains($geolocationStrategies, 'geolite2')) {
            return null;
        }

        return $io->ask(
            'Provide a GeoLite2 license key. Leave empty to disable geolocation with GeoLite2. '
            . '(Go to https://shlink.io/documentation/geolite-license-key to know how to generate it)',
        );
    }

    public function getDependentOption(): string
    {
        return GeolocationStrategiesConfigOption::class;
    }
}
