<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\RabbitMq;

use Override;
use Shlinkio\Shlink\Installer\Config\Option\DependentConfigOptionInterface;
use Shlinkio\Shlink\Installer\Config\Option\Server\AbstractAsyncRuntimeDependentConfigOption;

abstract class AbstractRabbitMqEnabledConfigOption extends AbstractAsyncRuntimeDependentConfigOption implements
    DependentConfigOptionInterface
{
    #[Override]
    public function shouldBeAsked(array $currentOptions): bool
    {
        $rabbitMqEnabled = $currentOptions[RabbitMqEnabledConfigOption::ENV_VAR] ?? false;
        return parent::shouldBeAsked($currentOptions) && $rabbitMqEnabled;
    }

    #[Override]
    public function getDependentOption(): string
    {
        return RabbitMqEnabledConfigOption::class;
    }
}
