<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\RabbitMq;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Symfony\Component\Console\Style\StyleInterface;

class RabbitMqPortConfigOption extends AbstractRabbitMqEnabledConfigOption
{
    public function getConfigPath(): array
    {
        return ['rabbitmq', 'port'];
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): int
    {
        return (int) $io->ask('RabbitMQ port', '5672');
    }
}
