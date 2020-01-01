<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Symfony\Component\Console\Style\SymfonyStyle;

class DatabaseHostConfigOption extends AbstractDatabaseDriverDependentConfigOption
{
    public function getConfigPath(): array
    {
        return ['entity_manager', 'connection', 'host'];
    }

    public function ask(SymfonyStyle $io, array $currentOptions)
    {
        return $io->ask('Database host', 'localhost');
    }
}
