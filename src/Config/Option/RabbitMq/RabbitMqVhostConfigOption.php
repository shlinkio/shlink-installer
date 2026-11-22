<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\RabbitMq;

use Override;
use Symfony\Component\Console\Style\StyleInterface;

class RabbitMqVhostConfigOption extends AbstractRabbitMqEnabledConfigOption
{
    #[Override]
    public function getEnvVar(): string
    {
        return 'RABBITMQ_VHOST';
    }

    #[Override]
    public function ask(StyleInterface $io, array $currentOptions): string
    {
        return $io->ask('RabbitMQ VHost', '/');
    }
}
