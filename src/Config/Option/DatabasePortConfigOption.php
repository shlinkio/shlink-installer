<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Symfony\Component\Console\Style\SymfonyStyle;

class DatabasePortConfigOption extends AbstractDatabaseDriverDependentConfigOption
{
    public function getConfigPath(): array
    {
        return ['entity_manager', 'connection', 'port'];
    }

    public function ask(SymfonyStyle $io, array $currentOptions)
    {
        return $io->ask('Database port', $this->getDefaultDbPort($currentOptions[DatabaseDriverConfigOption::class]));
    }

    private function getDefaultDbPort(string $driver): string
    {
        return $driver === 'pdo_mysql' ? '3306' : '5432';
    }
}
