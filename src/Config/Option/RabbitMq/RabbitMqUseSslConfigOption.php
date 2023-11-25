<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\RabbitMq;

use Symfony\Component\Console\Style\StyleInterface;

class RabbitMqUseSslConfigOption extends AbstractRabbitMqEnabledConfigOption
{
    public const ENV_VAR = 'RABBITMQ_USE_SSL';

    public function getEnvVar(): string
    {
        return self::ENV_VAR;
    }

    public function ask(StyleInterface $io, array $currentOptions): bool
    {
        return $io->confirm('Should use SSL?', false);
    }
}
