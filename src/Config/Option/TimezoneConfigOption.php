<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Override;
use Symfony\Component\Console\Style\StyleInterface;

class TimezoneConfigOption extends BaseConfigOption
{
    #[Override]
    public function getEnvVar(): string
    {
        return 'TIMEZONE';
    }

    #[Override]
    public function ask(StyleInterface $io, array $currentOptions): string|null
    {
        return $io->ask(
            'Set the timezone in which your Shlink instance is running (leave empty to use the one set in PHP config)',
        );
    }
}
