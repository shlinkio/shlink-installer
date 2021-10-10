<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidatorsTrait;
use Shlinkio\Shlink\Installer\Util\Utils;
use Symfony\Component\Console\Style\StyleInterface;

use function explode;
use function Functional\map;
use function sprintf;
use function trim;

// TODO Deprecated. Rename to RedisConfigOption
class RedisServersConfigOption extends BaseConfigOption
{
    use ConfigOptionsValidatorsTrait;

    public function getConfigPath(): array
    {
        return ['cache', 'redis'];
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): ?array
    {
        $useRedis = $io->confirm(
            'Do you want to use a redis instance, redis cluster or redis sentinels as a shared cache for Shlink? '
            . '(recommended if you run a cluster of Shlink instances)',
            false,
        );
        if (! $useRedis) {
            return null;
        }

        $sentinelService = $io->ask(
            'Provide the name of the sentinel service (leave empty if not using redis sentinel)',
        );
        $serves = $io->ask(sprintf(
            'Provide a comma-separated list of %s',
            $sentinelService === null
                ? 'redis server URIs. If more than one is provided, it will be considered a redis cluster'
                : 'sentinel instance URIs',
        ));

        return [
            'servers' => Utils::commaSeparatedToList($serves),
            'sentinel_service' => $sentinelService,
        ];
    }
}
