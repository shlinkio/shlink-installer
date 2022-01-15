<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\UrlShortener;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Util\AskUtilsTrait;
use Symfony\Component\Console\Style\StyleInterface;

class ShortDomainHostConfigOption extends BaseConfigOption
{
    use AskUtilsTrait;

    public function getDeprecatedPath(): array
    {
        return ['url_shortener', 'domain', 'hostname'];
    }

    public function getEnvVar(): string
    {
        return 'DEFAULT_DOMAIN';
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): string
    {
        return $this->askRequired($io, 'domain', 'Default domain for generated short URLs');
    }
}
