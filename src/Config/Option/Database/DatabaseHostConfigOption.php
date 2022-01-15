<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Database;

use Shlinkio\Shlink\Config\Collection\PathCollection;
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
        $extra = $dbDriver === DatabaseDriverConfigOption::POSTGRES_DRIVER ? ' (or unix socket)' : '';

        return $io->ask('Database host' . $extra, 'localhost');
    }
}
