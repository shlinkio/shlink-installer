<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Mercure;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\AbstractSwooleDependentConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\DependentConfigOptionInterface;

abstract class AbstractMercureEnabledConfigOption extends AbstractSwooleDependentConfigOption implements
    DependentConfigOptionInterface
{
    public function shouldBeAsked(PathCollection $currentOptions): bool
    {
        $enableMercure = $currentOptions->getValueInPath(EnableMercureConfigOption::CONFIG_PATH);

        return parent::shouldBeAsked($currentOptions) && $enableMercure;
    }

    public function getDependentOption(): string
    {
        return EnableMercureConfigOption::class;
    }
}
