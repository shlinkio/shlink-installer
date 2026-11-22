<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Matomo;

use Override;
use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\DependentConfigOptionInterface;

abstract class AbstractMatomoEnabledConfigOption extends BaseConfigOption implements DependentConfigOptionInterface
{
    #[Override]
    public function shouldBeAsked(array $currentOptions): bool
    {
        $matomoEnabled = $currentOptions[MatomoEnabledConfigOption::ENV_VAR] ?? false;
        return $matomoEnabled && parent::shouldBeAsked($currentOptions);
    }

    #[Override]
    public function getDependentOption(): string
    {
        return MatomoEnabledConfigOption::class;
    }
}
