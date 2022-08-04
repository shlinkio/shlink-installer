<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\UrlShortener;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class ShortDomainSchemaConfigOption extends BaseConfigOption
{
    public const ENV_VAR = 'IS_HTTPS_ENABLED';

    public function getEnvVar(): string
    {
        return self::ENV_VAR;
    }

    public function ask(StyleInterface $io, array $currentOptions): bool
    {
        return $io->confirm('Is HTTPS enabled on this server?');
    }
}
