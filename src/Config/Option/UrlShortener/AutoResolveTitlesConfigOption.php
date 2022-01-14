<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\UrlShortener;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class AutoResolveTitlesConfigOption extends BaseConfigOption
{
    public function getDeprecatedPath(): array
    {
        return ['url_shortener', 'auto_resolve_titles'];
    }

    public function getEnvVar(): string
    {
        return 'AUTO_RESOLVE_TITLES';
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): bool
    {
        return $io->confirm(
            'Do you want Shlink to resolve the short URL title based on the long URL \'s title tag (if any)? '
                . 'Otherwise, it will be kept empty unless explicitly provided.',
            false,
        );
    }
}
