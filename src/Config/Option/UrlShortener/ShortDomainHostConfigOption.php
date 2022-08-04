<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\UrlShortener;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Util\AskUtilsTrait;
use Symfony\Component\Console\Style\StyleInterface;

class ShortDomainHostConfigOption extends BaseConfigOption
{
    use AskUtilsTrait;

    public function getEnvVar(): string
    {
        return 'DEFAULT_DOMAIN';
    }

    public function ask(StyleInterface $io, array $currentOptions): string
    {
        return $this->askRequired($io, 'domain', 'Default domain for generated short URLs');
    }
}
