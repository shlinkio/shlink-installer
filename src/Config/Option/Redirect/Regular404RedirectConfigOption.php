<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Redirect;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidatorsTrait;
use Symfony\Component\Console\Style\StyleInterface;

class Regular404RedirectConfigOption extends BaseConfigOption
{
    use ConfigOptionsValidatorsTrait;

    public function getEnvVar(): string
    {
        return 'DEFAULT_REGULAR_404_REDIRECT';
    }

    public function ask(StyleInterface $io, array $currentOptions): ?string
    {
        return $io->ask(
            'Custom URL to redirect to when a user hits a not found URL other than an invalid short URL '
            . '(If no value is provided, the user will see a default "404 not found" page)',
            null,
            [$this, 'validateUrl'],
        );
    }
}
