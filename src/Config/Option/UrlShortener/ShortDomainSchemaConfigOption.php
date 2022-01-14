<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\UrlShortener;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class ShortDomainSchemaConfigOption extends BaseConfigOption
{
    public function getDeprecatedPath(): array
    {
        return ['url_shortener', 'domain', 'schema'];
    }

    public function getEnvVar(): string
    {
        return 'IS_HTTPS_ENABLED';
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): bool
    {
        return $io->confirm('Is HTTPS enabled on this server?');
    }
}
