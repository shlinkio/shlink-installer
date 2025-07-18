<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Mercure;

use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidator;
use Symfony\Component\Console\Style\StyleInterface;

class MercurePublicUrlConfigOption extends AbstractMercureEnabledConfigOption
{
    public function getEnvVar(): string
    {
        return 'MERCURE_PUBLIC_HUB_URL';
    }

    public function ask(StyleInterface $io, array $currentOptions): string
    {
        return $io->ask(
            'Public URL of the mercure hub server',
            validator: static fn ($value) => ConfigOptionsValidator::validateRequired($value, 'public hub URL'),
        );
    }
}
