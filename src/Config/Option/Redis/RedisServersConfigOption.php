<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Redis;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class RedisServersConfigOption extends BaseConfigOption
{
    public const string ENV_VAR = 'REDIS_SERVERS';

    public function getEnvVar(): string
    {
        return self::ENV_VAR;
    }

    public function ask(StyleInterface $io, array $currentOptions): string|null
    {
        $useRedis = $io->confirm(
            'Do you want to use a redis instance, redis cluster or redis sentinels as a shared cache for Shlink? '
            . '(recommended if you run a cluster of Shlink instances)',
            false,
        );
        if (! $useRedis) {
            return null;
        }

        return $io->ask(
            'Provide a comma-separated list of URIs (redis servers/sentinel instances). If they contains credentials '
            . 'with URL-reserved chars, make sure they are URL-encoded',
        );
    }
}
