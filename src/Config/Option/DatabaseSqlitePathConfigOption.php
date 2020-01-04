<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Shlinkio\Shlink\Installer\Util\PathCollection;
use Symfony\Component\Console\Style\SymfonyStyle;

class DatabaseSqlitePathConfigOption implements
    ConfigOptionInterface,
    DependentConfigOptionInterface
{
    public function getConfigPath(): array
    {
        return ['entity_manager', 'connection', 'path'];
    }

    public function ask(SymfonyStyle $io, PathCollection $currentOptions): string
    {
        return 'data/database.sqlite';
    }

    public function shouldBeAsked(PathCollection $currentOptions): bool
    {
        // FIXME We should not instantiate other plugin here
        $dbDriver = $currentOptions->getValueInPath((new DatabaseDriverConfigOption())->getConfigPath());
        return $dbDriver === DatabaseDriverConfigOption::SQLITE_DRIVER && ! $currentOptions->pathExists(
            $this->getConfigPath()
        );
    }

    public function getDependentOption(): string
    {
        return DatabaseDriverConfigOption::class;
    }
}
