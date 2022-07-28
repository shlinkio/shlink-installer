<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Database;

use Shlinkio\Shlink\Installer\Config\Util\DatabaseDriver;

use function str_contains;

abstract class AbstractNonSqliteDependentConfigOption extends AbstractDriverDependentConfigOption
{
    protected function shouldBeAskedForDbDriver(string $dbDriver): bool
    {
        // DEPRECATED.
        // Should just compare with strict equality (DatabaseDriver::tryFrom($dbDriver) === DatabaseDriver::SQLITE)
        // Using str_contains instead for backwards compatibility when importing the pdo_sqlite value
        return ! str_contains($dbDriver, DatabaseDriver::SQLITE->value);
    }
}
