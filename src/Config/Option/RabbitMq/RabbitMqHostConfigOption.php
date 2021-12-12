<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\RabbitMq;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Util\AskUtilsTrait;
use Symfony\Component\Console\Style\StyleInterface;

class RabbitMqHostConfigOption extends AbstractRabbitMqEnabledConfigOption
{
    use AskUtilsTrait;

    public function getConfigPath(): array
    {
        return ['rabbitmq', 'host'];
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): string
    {
        return $this->askRequired($io, 'RabbitMQ host name');
    }
}
