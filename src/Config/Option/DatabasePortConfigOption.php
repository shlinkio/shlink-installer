<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Shlinkio\Shlink\Installer\Util\PathCollection;
use Symfony\Component\Console\Style\SymfonyStyle;

class DatabasePortConfigOption extends AbstractNonSqliteDependentConfigOption
{
    public function getConfigPath(): array
    {
        return ['entity_manager', 'connection', 'port'];
    }

    public function ask(SymfonyStyle $io, PathCollection $currentOptions)
    {
        return $io->ask('Database port', $this->getDefaultDbPort($currentOptions->getValueInPath(
            // FIXME We should not instantiate a config plugin here
            (new DatabaseDriverConfigOption())->getConfigPath()
        )));
    }

    private function getDefaultDbPort(string $driver): string
    {
        return $driver === DatabaseDriverConfigOption::MYSQL_DRIVER ? '3306' : '5432';
    }
}
