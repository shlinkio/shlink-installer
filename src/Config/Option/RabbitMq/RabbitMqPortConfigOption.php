<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\RabbitMq;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidatorsTrait;
use Symfony\Component\Console\Style\StyleInterface;

class RabbitMqPortConfigOption extends AbstractRabbitMqEnabledConfigOption
{
    use ConfigOptionsValidatorsTrait;

    public function getDeprecatedPath(): array
    {
        return ['rabbitmq', 'port'];
    }

    public function getEnvVar(): string
    {
        return 'RABBITMQ_PORT';
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): int
    {
        return (int) $io->ask(
            'RabbitMQ port',
            '5672',
            fn (mixed $value) => $this->validateNumberBetween($value, 1, 65535),
        );
    }
}
