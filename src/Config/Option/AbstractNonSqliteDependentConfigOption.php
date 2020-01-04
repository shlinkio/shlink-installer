<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Shlinkio\Shlink\Installer\Util\PathCollection;

abstract class AbstractNonSqliteDependentConfigOption implements
    ConfigOptionInterface,
    DependentConfigOptionInterface
{
    public function getDependentOption(): string
    {
        return DatabaseDriverConfigOption::class;
    }

    public function shouldBeAsked(PathCollection $currentOptions, ?ConfigOptionInterface $dependantOption): bool
    {
        $currentOptionExists = $currentOptions->pathExists($this->getConfigPath());
        if ($dependantOption === null) {
            return ! $currentOptionExists;
        }

        $dbDriver = $currentOptions->getValueInPath($dependantOption->getConfigPath());
        return $dbDriver !== DatabaseDriverConfigOption::SQLITE_DRIVER && ! $currentOptionExists;
    }
}
