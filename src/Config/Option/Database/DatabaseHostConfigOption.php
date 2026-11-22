<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Database;

use Override;
use Shlinkio\Shlink\Installer\Config\Util\DatabaseDriver;
use Symfony\Component\Console\Style\StyleInterface;

class DatabaseHostConfigOption extends AbstractNonSqliteDependentConfigOption
{
    #[Override]
    public function getEnvVar(): string
    {
        return 'DB_HOST';
    }

    #[Override]
    public function ask(StyleInterface $io, array $currentOptions): string
    {
        $dbDriver = $currentOptions[DatabaseDriverConfigOption::ENV_VAR];
        $extra = DatabaseDriver::tryFrom($dbDriver) === DatabaseDriver::POSTGRES ? ' (or unix socket)' : '';

        return $io->ask('Database host' . $extra, 'localhost');
    }
}
