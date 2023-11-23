<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Redis;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\DependentConfigOptionInterface;
use Symfony\Component\Console\Style\StyleInterface;

/** @deprecated */
class RedisDecodeCredentialsConfigOption extends BaseConfigOption implements DependentConfigOptionInterface
{
    public function getEnvVar(): string
    {
        return 'REDIS_DECODE_CREDENTIALS';
    }

    public function shouldBeAsked(array $currentOptions): bool
    {
        $isRedisEnabled = $currentOptions[RedisServersConfigOption::ENV_VAR] ?? null;
        return $isRedisEnabled !== null && parent::shouldBeAsked($currentOptions);
    }

    public function ask(StyleInterface $io, array $currentOptions): bool
    {
        return $io->confirm(
            'Do you want redis credentials to be URL-decoded? '
            . '(If you provided servers with URL-encoded credentials, this should be "yes")',
            false,
        );
    }

    public function getDependentOption(): string
    {
        return RedisServersConfigOption::class;
    }
}
