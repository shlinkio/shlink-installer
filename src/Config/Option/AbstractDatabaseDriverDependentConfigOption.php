<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

abstract class AbstractDatabaseDriverDependentConfigOption implements
    ConfigOptionInterface,
    DependentConfigOptionInterface
{
    public function getDependentOption(): string
    {
        return DatabaseDriverConfigOption::class;
    }

    public function shouldBeAsked(array $currentOptions): bool
    {
        $dbDriver = $currentOptions[DatabaseDriverConfigOption::class] ?? null;
        return $dbDriver !== DatabaseDriverConfigOption::SQLITE_DRIVER && ! isset($currentOptions[static::class]);
    }
}
