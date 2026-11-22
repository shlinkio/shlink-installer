<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\RabbitMq;

use Override;
use Symfony\Component\Console\Style\StyleInterface;

class RabbitMqUseSslConfigOption extends AbstractRabbitMqEnabledConfigOption
{
    public const string ENV_VAR = 'RABBITMQ_USE_SSL';

    #[Override]
    public function getEnvVar(): string
    {
        return self::ENV_VAR;
    }

    #[Override]
    public function ask(StyleInterface $io, array $currentOptions): bool
    {
        return $io->confirm('Should use SSL?', false);
    }
}
