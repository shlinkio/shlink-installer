<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Symfony\Component\Console\Style\SymfonyStyle;

class DatabaseNameConfigOption extends AbstractDatabaseDriverDependentConfigOption
{
    public function getConfigPath(): array
    {
        return ['entity_manager', 'connection', 'dbname'];
    }

    public function ask(SymfonyStyle $io, array $currentOptions)
    {
        return $io->ask('Database name', 'shlink');
    }
}
