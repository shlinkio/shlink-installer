<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\UrlShortener;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidator;
use Symfony\Component\Console\Style\StyleInterface;

class ShortDomainHostConfigOption extends BaseConfigOption
{
    public function getEnvVar(): string
    {
        return 'DEFAULT_DOMAIN';
    }

    public function ask(StyleInterface $io, array $currentOptions): string
    {
        return $io->ask(
            'Default domain for generated short URLs',
            validator: static fn ($value) => ConfigOptionsValidator::validateRequired($value, 'domain'),
        );
    }
}
