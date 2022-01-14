<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Database;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

use function array_keys;

class DatabaseDriverConfigOption extends BaseConfigOption
{
    public const CONFIG_PATH = ['entity_manager', 'connection', 'driver'];
    public const MYSQL_DRIVER = 'pdo_mysql';
    public const POSTGRES_DRIVER = 'pdo_pgsql';
    public const SQLITE_DRIVER = 'pdo_sqlite';
    public const MSSQL_DRIVER = 'pdo_sqlsrv';
    private const DATABASE_DRIVERS = [
        'MySQL' => self::MYSQL_DRIVER,
        'MariaDB' => self::MYSQL_DRIVER,
        'PostgreSQL' => self::POSTGRES_DRIVER,
        'MicrosoftSQL' => self::MSSQL_DRIVER,
        'SQLite' => self::SQLITE_DRIVER,
    ];

    public function getDeprecatedPath(): array
    {
        return self::CONFIG_PATH;
    }

    public function getEnvVar(): string
    {
        return 'DB_DRIVER';
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): string
    {
        $databases = array_keys(self::DATABASE_DRIVERS);
        $dbType = $io->choice('Select database type', $databases, $databases[0]);
        return self::DATABASE_DRIVERS[$dbType];
    }
}
