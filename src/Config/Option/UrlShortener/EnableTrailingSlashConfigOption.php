<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\UrlShortener;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class EnableTrailingSlashConfigOption extends BaseConfigOption
{
    public function getEnvVar(): string
    {
        return 'SHORT_URL_TRAILING_SLASH';
    }

    public function ask(StyleInterface $io, array $currentOptions): bool
    {
        return $io->confirm(
            'Do you want to support trailing slashes in short URLs? (https://s.test/foo and https://s.test/foo/ '
            . 'will be considered the same)',
            false,
        );
    }
}
