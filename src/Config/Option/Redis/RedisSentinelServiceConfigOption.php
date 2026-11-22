<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Redis;

use Override;
use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\DependentConfigOptionInterface;
use Symfony\Component\Console\Style\StyleInterface;

class RedisSentinelServiceConfigOption extends BaseConfigOption implements DependentConfigOptionInterface
{
    public const string ENV_VAR = 'REDIS_SENTINEL_SERVICE';

    #[Override]
    public function getEnvVar(): string
    {
        return self::ENV_VAR;
    }

    #[Override]
    public function shouldBeAsked(array $currentOptions): bool
    {
        $isRedisEnabled = $currentOptions[RedisServersConfigOption::ENV_VAR] ?? null;
        return $isRedisEnabled !== null && parent::shouldBeAsked($currentOptions);
    }

    #[Override]
    public function ask(StyleInterface $io, array $currentOptions): string|null
    {
        return $io->ask('Provide the name of the sentinel service (leave empty if not using redis sentinel)');
    }

    #[Override]
    public function getDependentOption(): string
    {
        return RedisServersConfigOption::class;
    }
}
