<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Mercure;

use Symfony\Component\Console\Style\StyleInterface;

class MercureInternalUrlConfigOption extends AbstractMercureEnabledConfigOption
{
    public function getEnvVar(): string
    {
        return 'MERCURE_INTERNAL_HUB_URL';
    }

    public function ask(StyleInterface $io, array $currentOptions): ?string
    {
        return $io->ask('Internal URL of the mercure hub server (leave empty to use the public one)');
    }
}
