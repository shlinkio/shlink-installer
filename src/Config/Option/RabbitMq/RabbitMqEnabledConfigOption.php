<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\RabbitMq;

use Override;
use Shlinkio\Shlink\Installer\Config\Option\Server\AbstractAsyncRuntimeDependentConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class RabbitMqEnabledConfigOption extends AbstractAsyncRuntimeDependentConfigOption
{
    public const string ENV_VAR = 'RABBITMQ_ENABLED';

    #[Override]
    public function getEnvVar(): string
    {
        return self::ENV_VAR;
    }

    #[Override]
    public function ask(StyleInterface $io, array $currentOptions): bool
    {
        return $io->confirm('Do you want Shlink to publish real-time updates in a RabbitMQ instance?', false);
    }
}
