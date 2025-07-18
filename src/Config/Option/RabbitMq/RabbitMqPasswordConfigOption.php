<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\RabbitMq;

use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidator;
use Symfony\Component\Console\Style\StyleInterface;

class RabbitMqPasswordConfigOption extends AbstractRabbitMqEnabledConfigOption
{
    public function getEnvVar(): string
    {
        return 'RABBITMQ_PASSWORD';
    }

    public function ask(StyleInterface $io, array $currentOptions): string
    {
        $option = 'RabbitMQ password';
        return $io->ask(
            $option,
            validator: static fn ($value) => ConfigOptionsValidator::validateRequired($value, $option),
        );
    }
}
