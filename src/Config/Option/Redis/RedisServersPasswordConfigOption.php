<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Redis;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\DependentConfigOptionInterface;
use Symfony\Component\Console\Style\StyleInterface;

class RedisServersPasswordConfigOption extends BaseConfigOption implements DependentConfigOptionInterface
{
    public function getEnvVar(): string
    {
        return 'REDIS_SERVERS_PASSWORD';
    }

    public function shouldBeAsked(array $currentOptions): bool
    {
        $isSentinelEnabled = $currentOptions[RedisSentinelServiceConfigOption::ENV_VAR] ?? null;
        return $isSentinelEnabled !== null && parent::shouldBeAsked($currentOptions);
    }

    public function ask(StyleInterface $io, array $currentOptions): string|null
    {
        return $io->ask('Provide a password for your redis connection (leave empty if ACL is not required)');
    }

    public function getDependentOption(): string
    {
        return RedisSentinelServiceConfigOption::class;
    }
}
