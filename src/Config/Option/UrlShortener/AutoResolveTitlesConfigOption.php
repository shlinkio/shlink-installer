<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\UrlShortener;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class AutoResolveTitlesConfigOption extends BaseConfigOption
{
    public function getEnvVar(): string
    {
        return 'AUTO_RESOLVE_TITLES';
    }

    public function ask(StyleInterface $io, array $currentOptions): bool
    {
        return $io->confirm(
            'Do you want Shlink to resolve the short URL title based on the long URL\'s title tag (if any)? '
                . 'Otherwise, it will be kept empty unless explicitly provided.',
            false,
        );
    }
}
