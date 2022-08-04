<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Symfony\Component\Console\Style\StyleInterface;

class TimezoneConfigOption extends BaseConfigOption
{
    public function getEnvVar(): string
    {
        return 'TIMEZONE';
    }

    public function ask(StyleInterface $io, array $currentOptions): ?string
    {
        return $io->ask(
            'Set the timezone in which your Shlink instance is running (leave empty to use the one set in PHP config)',
        );
    }
}
