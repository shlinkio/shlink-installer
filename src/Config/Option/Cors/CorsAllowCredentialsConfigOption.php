<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Cors;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class CorsAllowCredentialsConfigOption extends BaseConfigOption
{
    public function getEnvVar(): string
    {
        return 'CORS_ALLOW_CREDENTIALS';
    }

    public function ask(StyleInterface $io, array $currentOptions): bool
    {
        return $io->confirm(
            'Do you want browsers to forward credentials to your Shlink server during CORS requests?',
            default: false,
        );
    }
}
