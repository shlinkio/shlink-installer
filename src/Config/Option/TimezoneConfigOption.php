<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Symfony\Component\Console\Style\StyleInterface;

class TimezoneConfigOption implements ConfigOptionInterface
{
    public function getEnvVar(): string
    {
        return 'TIMEZONE';
    }

    public function shouldBeAsked(PathCollection $currentOptions): bool
    {
        return ! $currentOptions->pathExists([$this->getEnvVar()]);
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): ?string
    {
        return $io->ask(
            'Set the timezone in which your Shlink instance is running (leave empty to use the one set in PHP config)',
        );
    }
}
