<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\UrlShortener;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class AppendExtraPathConfigOption extends BaseConfigOption
{
    public function getEnvVar(): string
    {
        return 'REDIRECT_APPEND_EXTRA_PATH';
    }

    public function ask(StyleInterface $io, array $currentOptions): bool
    {
        return $io->confirm(
            //@codingStandardsIgnoreStart
            <<<FOO
            Do you want Shlink to redirect short URLs as soon as the first segment of the path matches a short code, appending the rest to the long URL?
               * {shortDomain}/{shortCode}/[...extraPath] -> {longUrl}/[...extraPath]
               * https://example.com/abc123               -> https://www.twitter.com
               * https://example.com/abc123/shlinkio      -> https://www.twitter.com/shlinkio
               
            FOO,
            //@codingStandardsIgnoreEnd
            false,
        );
    }
}
