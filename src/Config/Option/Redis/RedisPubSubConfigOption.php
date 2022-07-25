<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Redis;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\ConfigOptionInterface;
use Shlinkio\Shlink\Installer\Config\Option\DependentConfigOptionInterface;
use Symfony\Component\Console\Style\StyleInterface;

class RedisPubSubConfigOption implements ConfigOptionInterface, DependentConfigOptionInterface
{
    public function getEnvVar(): string
    {
        return 'REDIS_PUB_SUB_ENABLED';
    }

    public function shouldBeAsked(PathCollection $currentOptions): bool
    {
        $isRedisEnabled = $currentOptions->getValueInPath([RedisServersConfigOption::ENV_VAR]);
        return $isRedisEnabled !== null && ! $currentOptions->pathExists([$this->getEnvVar()]);
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): bool
    {
        return $io->confirm('Do you want Shlink to publish real-time updates in this Redis instance/cluster?', false);
    }

    public function getDependentOption(): string
    {
        return RedisServersConfigOption::class;
    }
}
