<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Database;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\ConfigOptionInterface;
use Shlinkio\Shlink\Installer\Config\Option\DependentConfigOptionInterface;

abstract class AbstractDriverDependentConfigOption implements
    ConfigOptionInterface,
    DependentConfigOptionInterface
{
    public function getDependentOption(): string
    {
        return DatabaseDriverConfigOption::class;
    }

    public function shouldBeAsked(PathCollection $currentOptions): bool
    {
        $dbDriver = $currentOptions->getValueInPath(DatabaseDriverConfigOption::CONFIG_PATH);
        return $this->shouldBeAskedForDbDriver($dbDriver) && ! $currentOptions->pathExists($this->getConfigPath());
    }

    abstract protected function shouldBeAskedForDbDriver(string $dbDriver): bool;
}
