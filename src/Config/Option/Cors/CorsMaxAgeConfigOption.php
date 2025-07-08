<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Cors;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidator;
use Symfony\Component\Console\Style\StyleInterface;

class CorsMaxAgeConfigOption extends BaseConfigOption
{
    public function getEnvVar(): string
    {
        return 'CORS_MAX_AGE';
    }

    public function ask(StyleInterface $io, array $currentOptions): int
    {
        return $io->ask(
            'How long (in seconds) do you want CORS config to be cached by browsers?',
            '3600',
            ConfigOptionsValidator::validatePositiveNumber(...),
        );
    }
}
