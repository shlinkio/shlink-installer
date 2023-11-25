<?php

namespace Shlinkio\Shlink\Installer\Config\Option\Matomo;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\DependentConfigOptionInterface;

abstract class AbstractMatomoEnabledConfigOption extends BaseConfigOption implements DependentConfigOptionInterface
{
    public function shouldBeAsked(array $currentOptions): bool
    {
        $matomoEnabled = $currentOptions[MatomoEnabledConfigOption::ENV_VAR] ?? false;
        return $matomoEnabled && parent::shouldBeAsked($currentOptions);
    }

    public function getDependentOption(): string
    {
        return MatomoEnabledConfigOption::class;
    }
}
