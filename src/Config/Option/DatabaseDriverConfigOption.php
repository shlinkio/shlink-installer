<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Shlinkio\Shlink\Installer\Util\PathCollection;
use Symfony\Component\Console\Style\SymfonyStyle;

use function array_keys;

class DatabaseDriverConfigOption extends BaseConfigOption
{
    public const MYSQL_DRIVER = 'pdo_mysql';
    public const SQLITE_DRIVER = 'pdo_sqlite';
    private const DATABASE_DRIVERS = [
        'MySQL' => self::MYSQL_DRIVER,
        'MariaDB' => self::MYSQL_DRIVER,
        'PostgreSQL' => 'pdo_pgsql',
        'SQLite' => self::SQLITE_DRIVER,
    ];

    public function getConfigPath(): array
    {
        return ['entity_manager', 'connection', 'driver'];
    }

    public function ask(SymfonyStyle $io, PathCollection $currentOptions, ?ConfigOptionInterface $dependentOption)
    {
        $databases = array_keys(self::DATABASE_DRIVERS);
        $dbType = $io->choice('Select database type', $databases, $databases[0]);
        return self::DATABASE_DRIVERS[$dbType];
    }
}
