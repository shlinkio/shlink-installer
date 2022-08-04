<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Redirect;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidatorsTrait;
use Symfony\Component\Console\Style\StyleInterface;

class BaseUrlRedirectConfigOption extends BaseConfigOption
{
    use ConfigOptionsValidatorsTrait;

    public function getEnvVar(): string
    {
        return 'DEFAULT_BASE_URL_REDIRECT';
    }

    public function ask(StyleInterface $io, array $currentOptions): ?string
    {
        return $io->ask(
            'Custom URL to redirect to when a user hits Shlink\'s base URL (If no value is provided, the '
            . 'user will see a default "404 not found" page)',
            null,
            [$this, 'validateUrl'],
        );
    }
}
