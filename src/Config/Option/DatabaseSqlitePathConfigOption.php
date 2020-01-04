<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Shlinkio\Shlink\Installer\Util\PathCollection;
use Symfony\Component\Console\Style\SymfonyStyle;

class DatabaseSqlitePathConfigOption extends AbstractDriverDependentConfigOption
{
    public function getConfigPath(): array
    {
        return ['entity_manager', 'connection', 'path'];
    }

    public function ask(
        SymfonyStyle $io,
        PathCollection $currentOptions,
        ?ConfigOptionInterface $dependantOption
    ): string {
        return 'data/database.sqlite';
    }

    protected function shouldBeAskedForDbDriver(string $dbDriver): bool
    {
        return $dbDriver === DatabaseDriverConfigOption::SQLITE_DRIVER;
    }
}
