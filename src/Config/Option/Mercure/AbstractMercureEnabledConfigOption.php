<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Mercure;

use Shlinkio\Shlink\Installer\Config\Option\DependentConfigOptionInterface;
use Shlinkio\Shlink\Installer\Config\Option\Server\AbstractAsyncRuntimeDependentConfigOption;

abstract class AbstractMercureEnabledConfigOption extends AbstractAsyncRuntimeDependentConfigOption implements
    DependentConfigOptionInterface
{
    public function shouldBeAsked(array $currentOptions): bool
    {
        $enableMercure = $currentOptions[EnableMercureConfigOption::ENV_VAR] ?? false;
        return parent::shouldBeAsked($currentOptions) && $enableMercure;
    }

    public function getDependentOption(): string
    {
        return EnableMercureConfigOption::class;
    }
}
