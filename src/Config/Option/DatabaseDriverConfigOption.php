<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Symfony\Component\Console\Style\SymfonyStyle;

use function array_keys;

class DatabaseDriverConfigOption extends BaseConfigOption
{
    public const SQLITE_DRIVER = 'pdo_sqlite';
    private const DATABASE_DRIVERS = [
        'MySQL' => 'pdo_mysql',
        'MariaDB' => 'pdo_mysql',
        'PostgreSQL' => 'pdo_pgsql',
        'SQLite' => self::SQLITE_DRIVER,
    ];

    public function getConfigPath(): array
    {
        return ['entity_manager', 'connection', 'driver'];
    }

    public function ask(SymfonyStyle $io, array $currentOptions)
    {
        $databases = array_keys(self::DATABASE_DRIVERS);
        $dbType = $io->choice('Select database type', $databases, $databases[0]);
        return self::DATABASE_DRIVERS[$dbType];
    }
}
