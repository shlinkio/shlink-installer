<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Shlinkio\Shlink\Installer\Util\PathCollection;
use Symfony\Component\Console\Style\SymfonyStyle;

class DatabaseHostConfigOption extends AbstractNonSqliteDependentConfigOption
{
    public function getConfigPath(): array
    {
        return ['entity_manager', 'connection', 'host'];
    }

    public function ask(SymfonyStyle $io, PathCollection $currentOptions, ?ConfigOptionInterface $dependentOption)
    {
        return $io->ask('Database host', 'localhost');
    }
}
