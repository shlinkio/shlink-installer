<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\RabbitMq;

use Shlinkio\Shlink\Installer\Config\Option\AbstractSwooleDependentConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class RabbitMqEnabledConfigOption extends AbstractSwooleDependentConfigOption
{
    public const ENV_VAR = 'RABBITMQ_ENABLED';

    public function getEnvVar(): string
    {
        return self::ENV_VAR;
    }

    public function ask(StyleInterface $io, array $currentOptions): bool
    {
        return $io->confirm('Do you want Shlink to publish real-time updates in a RabbitMQ instance?', false);
    }
}
