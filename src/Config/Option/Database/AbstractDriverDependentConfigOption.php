<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Database;

use Override;
use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\DependentConfigOptionInterface;

abstract class AbstractDriverDependentConfigOption extends BaseConfigOption implements DependentConfigOptionInterface
{
    #[Override]
    public function getDependentOption(): string
    {
        return DatabaseDriverConfigOption::class;
    }

    #[Override]
    public function shouldBeAsked(array $currentOptions): bool
    {
        $dbDriver = $currentOptions[DatabaseDriverConfigOption::ENV_VAR];
        return $this->shouldBeAskedForDbDriver($dbDriver) && parent::shouldBeAsked($currentOptions);
    }

    abstract protected function shouldBeAskedForDbDriver(string $dbDriver): bool;
}
