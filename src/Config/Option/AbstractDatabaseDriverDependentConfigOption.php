<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Shlinkio\Shlink\Installer\Util\PathCollection;

abstract class AbstractDatabaseDriverDependentConfigOption implements
    ConfigOptionInterface,
    DependentConfigOptionInterface
{
    public function getDependentOption(): string
    {
        return DatabaseDriverConfigOption::class;
    }

    public function shouldBeAsked(PathCollection $currentOptions): bool
    {
        // FIXME We should not instantiate other plugin here
        $dbDriver = $currentOptions->getValueInPath((new DatabaseDriverConfigOption())->getConfigPath());
        return $dbDriver !== DatabaseDriverConfigOption::SQLITE_DRIVER && ! $currentOptions->pathExists(
            $this->getConfigPath()
        );
    }
}
