<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Database;

abstract class AbstractNonSqliteDependentConfigOption extends AbstractDriverDependentConfigOption
{
    protected function shouldBeAskedForDbDriver(string $dbDriver): bool
    {
        return $dbDriver !== DatabaseDriverConfigOption::SQLITE_DRIVER;
    }
}
