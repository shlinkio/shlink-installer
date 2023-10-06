<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Cache;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class CacheNamespaceConfigOption extends BaseConfigOption
{
    public function getEnvVar(): string
    {
        return 'CACHE_NAMESPACE';
    }

    public function ask(StyleInterface $io, array $currentOptions): string
    {
        return $io->ask(
            'Prefix for cache entry keys. (Change this if you run multiple Shlink instances on this server, or they '
            . 'share the same redis instance)',
            'Shlink',
        );
    }
}
