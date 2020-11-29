<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Database;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Symfony\Component\Console\Style\StyleInterface;

class DatabaseUnixSocketConfigOption extends AbstractDriverDependentConfigOption
{
    public function getConfigPath(): array
    {
        return ['entity_manager', 'connection', 'unix_socket'];
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): ?string
    {
        return $io->ask('Unix socket (leave empty to not use a socket)');
    }

    protected function shouldBeAskedForDbDriver(string $dbDriver): bool
    {
        return $dbDriver === DatabaseDriverConfigOption::MYSQL_DRIVER;
    }
}
