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

    public function shouldBeAsked(PathCollection $currentOptions): bool
    {
        // FIXME We should not instantiate other plugin here
        $dbDriver = $currentOptions->getValueInPath((new DatabaseDriverConfigOption())->getConfigPath());
        return $dbDriver === DatabaseDriverConfigOption::MYSQL_DRIVER && ! $currentOptions->pathExists(
            $this->getConfigPath()
        );
    }

    public function ask(SymfonyStyle $io, PathCollection $currentOptions): array
    {
        return [
            // PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            1002 => 'SET NAMES utf8',
        ];
    }

    public function getDependentOption(): string
    {
        // TODO: Implement getDependentOption() method.
    }
}
