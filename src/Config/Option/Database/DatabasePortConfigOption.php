<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Database;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Util\DatabaseDriver;
use Symfony\Component\Console\Style\StyleInterface;

class DatabasePortConfigOption extends AbstractNonSqliteDependentConfigOption
{
    public function getDeprecatedPath(): array
    {
        return ['entity_manager', 'connection', 'port'];
    }

    public function getEnvVar(): string
    {
        return 'DB_PORT';
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): string
    {
        return $io->ask('Database port', $this->getDefaultDbPortForDriver($currentOptions->getValueInPath(
            DatabaseDriverConfigOption::CONFIG_PATH,
        )));
    }

    private function getDefaultDbPortForDriver(string $driver): string
    {
        return DatabaseDriver::tryFrom($driver)?->defaultPort() ?? '';
    }
}
