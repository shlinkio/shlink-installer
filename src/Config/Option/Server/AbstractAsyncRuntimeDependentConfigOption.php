<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Server;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\DependentConfigOptionInterface;
use Shlinkio\Shlink\Installer\Config\Util\RuntimeType;

abstract class AbstractAsyncRuntimeDependentConfigOption extends BaseConfigOption implements
    DependentConfigOptionInterface
{
    public function shouldBeAsked(array $currentOptions): bool
    {
        $runtime = $currentOptions[RuntimeConfigOption::ENV_VAR] ?? '';
        return RuntimeType::tryFrom($runtime) === RuntimeType::ASYNC && parent::shouldBeAsked($currentOptions);
    }

    public function getDependentOption(): string
    {
        return RuntimeConfigOption::class;
    }
}
