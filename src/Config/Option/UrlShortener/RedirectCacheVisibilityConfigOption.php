<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\UrlShortener;

use Symfony\Component\Console\Style\StyleInterface;

class RedirectCacheVisibilityConfigOption extends AbstractPermanentRedirectDependentConfigOption
{
    public function getEnvVar(): string
    {
        return 'REDIRECT_CACHE_VISIBILITY';
    }

    public function ask(StyleInterface $io, array $currentOptions): string
    {
        return $io->confirm('Do you want redirects to be cached by reverse proxies?', default: false)
            ? 'public'
            : 'private';
    }
}
