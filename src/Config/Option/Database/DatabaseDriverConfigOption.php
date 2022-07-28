<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Database;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Config\Util\DatabaseDriver;
use Symfony\Component\Console\Style\StyleInterface;

use function array_keys;

class DatabaseDriverConfigOption extends BaseConfigOption
{
    public const ENV_VAR = 'DB_DRIVER';
    public const CONFIG_PATH = [self::ENV_VAR];
    private const DATABASE_DRIVERS = [
        'MySQL' => DatabaseDriver::MYSQL,
        'MariaDB' => DatabaseDriver::MYSQL,
        'PostgreSQL' => DatabaseDriver::POSTGRES,
        'MicrosoftSQL' => DatabaseDriver::MSSQL,
        'SQLite' => DatabaseDriver::SQLITE,
    ];

    public function getDeprecatedPath(): array
    {
        return ['entity_manager', 'connection', 'driver'];
    }

    public function getEnvVar(): string
    {
        return 'DB_DRIVER';
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): string
    {
        $databases = array_keys(self::DATABASE_DRIVERS);
        $dbType = $io->choice('Select database type', $databases, $databases[0]);
        return self::DATABASE_DRIVERS[$dbType]->value;
    }
}
