<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Database;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Util\DatabaseDriver;
use Symfony\Component\Console\Style\StyleInterface;

class DatabaseHostConfigOption extends AbstractNonSqliteDependentConfigOption
{
    public function getDeprecatedPath(): array
    {
        return ['entity_manager', 'connection', 'host'];
    }

    public function getEnvVar(): string
    {
        return 'DB_HOST';
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): string
    {
        $dbDriver = $currentOptions->getValueInPath(DatabaseDriverConfigOption::CONFIG_PATH);
        $extra = DatabaseDriver::tryFrom($dbDriver) === DatabaseDriver::POSTGRES ? ' (or unix socket)' : '';

        return $io->ask('Database host' . $extra, 'localhost');
    }
}
