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

    public function ask(
        SymfonyStyle $io,
        PathCollection $currentOptions,
        ?ConfigOptionInterface $dependantOption
    ): string {
        return 'data/database.sqlite';
    }

    public function shouldBeAsked(PathCollection $currentOptions, ?ConfigOptionInterface $dependantOption): bool
    {
        $currentOptionExists = $currentOptions->pathExists($this->getConfigPath());
        if ($dependantOption === null) {
            return ! $currentOptionExists;
        }

        $dbDriver = $currentOptions->getValueInPath($dependantOption->getConfigPath());
        return $dbDriver === DatabaseDriverConfigOption::SQLITE_DRIVER && ! $currentOptionExists;
    }

    public function getDependentOption(): string
    {
        return DatabaseDriverConfigOption::class;
    }
}
