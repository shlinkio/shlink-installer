<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Mercure;

use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidator;
use Symfony\Component\Console\Style\StyleInterface;

class MercureJwtSecretConfigOption extends AbstractMercureEnabledConfigOption
{
    public function getEnvVar(): string
    {
        return 'MERCURE_JWT_SECRET';
    }

    public function ask(StyleInterface $io, array $currentOptions): string
    {
        return $io->ask(
            'The secret key known by the mercure hub server to validate JWTs',
            validator: static fn ($value) => ConfigOptionsValidator::validateRequired($value, 'JWT secret'),
        );
    }
}
