<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Geolocation;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

use function sprintf;

class GeolocationStrategiesConfigOption extends BaseConfigOption
{
    public const string ENV_VAR = 'GEOLOCATION_STRATEGIES';

    private const string GEOLITE_STRATEGY = 'geolite2';
    private const string CLOUDFLARE_STRATEGY = 'cloudflare';

    public function getEnvVar(): string
    {
        return self::ENV_VAR;
    }

    public function ask(StyleInterface $io, array $currentOptions): string
    {
        $useGeolite = $io->confirm('Do you want to geolocate visitors using a GeoLite2 database?');
        $useCloudflare = $io->confirm('Do you want to geolocate visitors using Cloudflare-provided headers?');

        if ($useGeolite && $useCloudflare) {
            $firstOption = $io->choice('What do you want to try first to geolocate visitors?', [
                'GeoLite2',
                'Cloudflare',
            ]);
            return $firstOption === 'GeoLite2'
                ? sprintf('%s,%s', self::GEOLITE_STRATEGY, self::CLOUDFLARE_STRATEGY)
                : sprintf('%s,%s', self::CLOUDFLARE_STRATEGY, self::GEOLITE_STRATEGY);
        }

        if ($useGeolite) {
            return self::GEOLITE_STRATEGY;
        }

        return self::CLOUDFLARE_STRATEGY;
    }
}
