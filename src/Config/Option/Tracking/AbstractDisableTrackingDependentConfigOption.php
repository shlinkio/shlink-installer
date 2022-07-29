<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Tracking;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\DependentConfigOptionInterface;

abstract class AbstractDisableTrackingDependentConfigOption extends BaseConfigOption implements
    DependentConfigOptionInterface
{
    public function getDependentOption(): string
    {
        return DisableTrackingConfigOption::class;
    }

    public function shouldBeAsked(array $currentOptions): bool
    {
        $disableTracking = $currentOptions[DisableTrackingConfigOption::ENV_VAR] ?? false;
        return ! $disableTracking && parent::shouldBeAsked($currentOptions);
    }
}
