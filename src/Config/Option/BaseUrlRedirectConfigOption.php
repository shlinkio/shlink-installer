<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidatorsTrait;
use Shlinkio\Shlink\Installer\Util\PathCollection;
use Symfony\Component\Console\Style\StyleInterface;

class BaseUrlRedirectConfigOption extends BaseConfigOption
{
    use ConfigOptionsValidatorsTrait;

    public function getConfigPath(): array
    {
        return ['not_found_redirects', 'base_url'];
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): ?string
    {
        return $io->ask(
            'Custom URL to redirect to when a user hits Shlink\'s base URL (If no value is provided, the '
            . 'user will see a default "404 not found" page)',
            null,
            [$this, 'validateUrl']
        );
    }
}
