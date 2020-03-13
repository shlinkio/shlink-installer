<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Shlinkio\Shlink\Config\Collection\PathCollection;

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
