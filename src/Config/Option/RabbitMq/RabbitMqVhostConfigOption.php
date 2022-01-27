<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\RabbitMq;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Symfony\Component\Console\Style\StyleInterface;

class RabbitMqVhostConfigOption extends AbstractRabbitMqEnabledConfigOption
{
    public function getDeprecatedPath(): array
    {
        return ['rabbitmq', 'vhost'];
    }

    public function getEnvVar(): string
    {
        return 'RABBITMQ_VHOST';
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): string
    {
        return $io->ask('RabbitMQ VHost', '/');
    }
}
