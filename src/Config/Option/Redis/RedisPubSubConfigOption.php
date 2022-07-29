<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Redis;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\DependentConfigOptionInterface;
use Symfony\Component\Console\Style\StyleInterface;

class RedisPubSubConfigOption extends BaseConfigOption implements DependentConfigOptionInterface
{
    public function getEnvVar(): string
    {
        return 'REDIS_PUB_SUB_ENABLED';
    }

    public function shouldBeAsked(array $currentOptions): bool
    {
        $isRedisEnabled = $currentOptions[RedisServersConfigOption::ENV_VAR] ?? null;
        return $isRedisEnabled !== null && parent::shouldBeAsked($currentOptions);
    }

    public function ask(StyleInterface $io, array $currentOptions): bool
    {
        return $io->confirm('Do you want Shlink to publish real-time updates in this Redis instance/cluster?', false);
    }

    public function getDependentOption(): string
    {
        return RedisServersConfigOption::class;
    }
}
