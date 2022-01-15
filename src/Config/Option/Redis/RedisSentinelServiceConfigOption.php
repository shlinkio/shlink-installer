<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Redis;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\DependentConfigOptionInterface;
use Symfony\Component\Console\Style\StyleInterface;

class RedisSentinelServiceConfigOption extends BaseConfigOption implements DependentConfigOptionInterface
{
    public function getDeprecatedPath(): array
    {
        return ['cache', 'redis', 'sentinel_service'];
    }

    public function getEnvVar(): string
    {
        return 'REDIS_SENTINEL_SERVICE';
    }

    public function shouldBeAsked(PathCollection $currentOptions): bool
    {
        $isRedisEnabled = $currentOptions->getValueInPath([RedisServersConfigOption::ENV_VAR]);
        return $isRedisEnabled !== null && parent::shouldBeAsked($currentOptions);
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): ?string
    {
        return $io->ask('Provide the name of the sentinel service (leave empty if not using redis sentinel)');
    }

    public function getDependentOption(): string
    {
        return RedisServersConfigOption::class;
    }
}
