<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\RabbitMq;

use Symfony\Component\Console\Style\StyleInterface;

class RabbitMqVhostConfigOption extends AbstractRabbitMqEnabledConfigOption
{
    public function getEnvVar(): string
    {
        return 'RABBITMQ_VHOST';
    }

    public function ask(StyleInterface $io, array $currentOptions): string
    {
        return $io->ask('RabbitMQ VHost', '/');
    }
}
