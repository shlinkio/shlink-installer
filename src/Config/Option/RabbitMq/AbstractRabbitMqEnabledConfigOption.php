<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\RabbitMq;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\AbstractSwooleDependentConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\DependentConfigOptionInterface;

abstract class AbstractRabbitMqEnabledConfigOption extends AbstractSwooleDependentConfigOption implements
    DependentConfigOptionInterface
{
    public function shouldBeAsked(PathCollection $currentOptions): bool
    {
        $rabbitMqEnabled = $currentOptions->getValueInPath(RabbitMqEnabledConfigOption::CONFIG_PATH);

        return parent::shouldBeAsked($currentOptions) && $rabbitMqEnabled;
    }

    public function getDependentOption(): string
    {
        return RabbitMqEnabledConfigOption::class;
    }
}
