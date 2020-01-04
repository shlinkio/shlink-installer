<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Shlinkio\Shlink\Installer\Util\PathCollection;
use Symfony\Component\Console\Style\StyleInterface;

class DatabasePortConfigOption extends AbstractNonSqliteDependentConfigOption
{
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
        return $driver === DatabaseDriverConfigOption::MYSQL_DRIVER ? '3306' : '5432';
    }
}
