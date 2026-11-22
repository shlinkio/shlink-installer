<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Server;

use Override;
use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\DependentConfigOptionInterface;
use Shlinkio\Shlink\Installer\Config\Util\RuntimeType;

abstract class AbstractAsyncRuntimeDependentConfigOption extends BaseConfigOption implements
    DependentConfigOptionInterface
{
    #[Override]
    public function shouldBeAsked(array $currentOptions): bool
    {
        $runtime = $currentOptions[RuntimeConfigOption::ENV_VAR] ?? '';
        return RuntimeType::tryFrom($runtime) === RuntimeType::ASYNC && parent::shouldBeAsked($currentOptions);
    }

    #[Override]
    public function getDependentOption(): string
    {
        return RuntimeConfigOption::class;
    }
}
