<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Shlinkio\Shlink\Installer\Util\PathCollection;
use Symfony\Component\Console\Style\SymfonyStyle;

class DatabaseMySqlOptionsConfigOption implements
    ConfigOptionInterface,
    DependentConfigOptionInterface
{
    public function getConfigPath(): array
    {
        return ['entity_manager', 'connection', 'driverOptions'];
    }

    public function shouldBeAsked(PathCollection $currentOptions, ?ConfigOptionInterface $dependantOption): bool
    {
        $currentOptionExists = $currentOptions->pathExists($this->getConfigPath());
        if ($dependantOption === null) {
            return ! $currentOptionExists;
        }

        $dbDriver = $currentOptions->getValueInPath($dependantOption->getConfigPath());
        return $dbDriver === DatabaseDriverConfigOption::MYSQL_DRIVER && ! $currentOptionExists;
    }

    public function ask(
        SymfonyStyle $io,
        PathCollection $currentOptions,
        ?ConfigOptionInterface $dependantOption
    ): array {
        return [
            // PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            1002 => 'SET NAMES utf8',
        ];
    }

    public function getDependentOption(): string
    {
        return DatabaseDriverConfigOption::class;
    }
}
