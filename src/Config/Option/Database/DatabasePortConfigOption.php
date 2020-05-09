<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Database;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Symfony\Component\Console\Style\StyleInterface;

class DatabasePortConfigOption extends AbstractNonSqliteDependentConfigOption
{
    private const DRIVER_PORT_MAPPING = [
        DatabaseDriverConfigOption::MYSQL_DRIVER => '3306',
        DatabaseDriverConfigOption::POSTGRES_DRIVER => '5432',
        DatabaseDriverConfigOption::MSSQL_DRIVER => '1433',
    ];

    public function getConfigPath(): array
    {
        return ['entity_manager', 'connection', 'port'];
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): string
    {
        return $io->ask('Database port', $this->getDefaultDbPortForDriver($currentOptions->getValueInPath(
            DatabaseDriverConfigOption::CONFIG_PATH,
        )));
    }

    private function getDefaultDbPortForDriver(string $driver): string
    {
        return self::DRIVER_PORT_MAPPING[$driver] ?? '';
    }
}
