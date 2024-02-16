<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Server;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Config\Util\RuntimeType;
use Symfony\Component\Console\Style\StyleInterface;

use function array_keys;

class RuntimeConfigOption extends BaseConfigOption
{
    public const ENV_VAR = 'RUNTIME';
    private const RUNTIMES = [
        'RoadRunner' => RuntimeType::ASYNC,
        'Classic web server (Nginx, Apache, etc)' => RuntimeType::REGULAR,
    ];

    public function getEnvVar(): string
    {
        return self::ENV_VAR;
    }

    public function ask(StyleInterface $io, array $currentOptions): string
    {
        $runtimes = array_keys(self::RUNTIMES);
        $runtime = $io->choice(
            'Select the runtime you are planning to use to serve Shlink (this is only used to conditionally skip some '
            . 'follow-up questions)',
            $runtimes,
            $runtimes[0],
        );

        return self::RUNTIMES[$runtime]->value;
    }
}
