<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Database;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\DependentConfigOptionInterface;

abstract class AbstractDriverDependentConfigOption extends BaseConfigOption implements
    DependentConfigOptionInterface
{
    public function getDependentOption(): string
    {
        return DatabaseDriverConfigOption::class;
    }

    public function shouldBeAsked(PathCollection $currentOptions): bool
    {
        $dbDriver = $currentOptions->getValueInPath(DatabaseDriverConfigOption::CONFIG_PATH);
        return $this->shouldBeAskedForDbDriver($dbDriver) && parent::shouldBeAsked($currentOptions);
    }

    abstract protected function shouldBeAskedForDbDriver(string $dbDriver): bool;
}
