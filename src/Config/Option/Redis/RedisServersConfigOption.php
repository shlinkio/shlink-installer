<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Redis;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Util\Utils;
use Symfony\Component\Console\Style\StyleInterface;

class RedisServersConfigOption extends BaseConfigOption
{
    public const ENV_VAR = 'REDIS_SERVERS';

    public function getDeprecatedPath(): array
    {
        return ['cache', 'redis', 'servers'];
    }

    public function getEnvVar(): string
    {
        return self::ENV_VAR;
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

        $serves = $io->ask('Provide a comma-separated list of URIs (redis servers/sentinel instances)');

        return $serves === null ? null : Utils::commaSeparatedToList($serves);
    }
}
