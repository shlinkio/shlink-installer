<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Database;

use Shlinkio\Shlink\Installer\Config\Option\Database\AbstractDriverDependentConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\Database\DatabaseDriverConfigOption;

abstract class AbstractNonSqliteDependentConfigOption extends AbstractDriverDependentConfigOption
{
    protected function shouldBeAskedForDbDriver(string $dbDriver): bool
    {
        return $dbDriver !== DatabaseDriverConfigOption::SQLITE_DRIVER;
    }
}
