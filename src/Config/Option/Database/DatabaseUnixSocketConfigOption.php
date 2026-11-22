<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Database;

use Override;
use Shlinkio\Shlink\Installer\Config\Util\DatabaseDriver;
use Symfony\Component\Console\Style\StyleInterface;

class DatabaseUnixSocketConfigOption extends AbstractDriverDependentConfigOption
{
    #[Override]
    public function getEnvVar(): string
    {
        return 'DB_UNIX_SOCKET';
    }

    #[Override]
    public function ask(StyleInterface $io, array $currentOptions): string|null
    {
        return $io->ask('Unix socket (leave empty to not use a socket)');
    }

    #[Override]
    protected function shouldBeAskedForDbDriver(string $dbDriver): bool
    {
        return DatabaseDriver::tryFrom($dbDriver) === DatabaseDriver::MYSQL;
    }
}
