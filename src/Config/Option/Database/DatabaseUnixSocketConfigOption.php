<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Database;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Util\DatabaseDriver;
use Symfony\Component\Console\Style\StyleInterface;

class DatabaseUnixSocketConfigOption extends AbstractDriverDependentConfigOption
{
    public function getDeprecatedPath(): array
    {
        return ['entity_manager', 'connection', 'unix_socket'];
    }

    public function getEnvVar(): string
    {
        return 'DB_UNIX_SOCKET';
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): ?string
    {
        return $io->ask('Unix socket (leave empty to not use a socket)');
    }

    protected function shouldBeAskedForDbDriver(string $dbDriver): bool
    {
        return DatabaseDriver::tryFrom($dbDriver) === DatabaseDriver::MYSQL;
    }
}
