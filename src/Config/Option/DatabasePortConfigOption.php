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

    public function ask(SymfonyStyle $io, PathCollection $currentOptions, ?ConfigOptionInterface $dependentOption)
    {
        return $io->ask('Database port', $this->getDefaultDbPort($currentOptions->getValueInPath(
            $dependentOption !== null ? $dependentOption->getConfigPath() : DatabaseDriverConfigOption::MYSQL_DRIVER
        )));
    }

    private function getDefaultDbPort(string $driver): string
    {
        return $driver === DatabaseDriverConfigOption::MYSQL_DRIVER ? '3306' : '5432';
    }
}
