<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\UrlShortener;

use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidator;
use Symfony\Component\Console\Style\StyleInterface;

class RedirectCacheLifeTimeConfigOption extends AbstractPermanentRedirectDependentConfigOption
{
    public function getEnvVar(): string
    {
        return 'REDIRECT_CACHE_LIFETIME';
    }

    public function ask(StyleInterface $io, array $currentOptions): int
    {
        return $io->ask(
            'How long (in seconds) do you want your redirects to be cached by visitors?',
            '30',
            ConfigOptionsValidator::validatePositiveNumber(...),
        );
    }
}
