<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Redirect;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidator;
use Symfony\Component\Console\Style\StyleInterface;

class InvalidShortUrlRedirectConfigOption extends BaseConfigOption
{
    public function getEnvVar(): string
    {
        return 'DEFAULT_INVALID_SHORT_URL_REDIRECT';
    }

    public function ask(StyleInterface $io, array $currentOptions): string|null
    {
        return $io->ask(
            'Custom URL to redirect to when a user hits an invalid short URL (If no value is provided, the '
            . 'user will see a default "404 not found" page)',
            null,
            ConfigOptionsValidator::validateUrl(...),
        );
    }
}
