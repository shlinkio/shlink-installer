<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\RabbitMq;

use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidator;
use Symfony\Component\Console\Style\StyleInterface;

class RabbitMqUserConfigOption extends AbstractRabbitMqEnabledConfigOption
{
    public function getEnvVar(): string
    {
        return 'RABBITMQ_USER';
    }

    public function ask(StyleInterface $io, array $currentOptions): string
    {
        $option = 'RabbitMQ username';
        return $io->ask(
            $option,
            validator: static fn ($value) => ConfigOptionsValidator::validateRequired($value, $option),
        );
    }
}
