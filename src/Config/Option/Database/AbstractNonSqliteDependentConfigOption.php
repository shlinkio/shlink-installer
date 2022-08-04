<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Database;

use Shlinkio\Shlink\Installer\Config\Util\DatabaseDriver;

abstract class AbstractNonSqliteDependentConfigOption extends AbstractDriverDependentConfigOption
{
    protected function shouldBeAskedForDbDriver(string $dbDriver): bool
    {
        return DatabaseDriver::tryFrom($dbDriver) !== DatabaseDriver::SQLITE;
    }
}
