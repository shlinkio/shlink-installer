<?php

namespace Shlinkio\Shlink\Installer\Config\Option\Mercure;

use Symfony\Component\Console\Style\StyleInterface;

class MercureVersionConfigOption extends AbstractMercureEnabledConfigOption
{
    public function getEnvVar(): string
    {
        return 'MERCURE_VERSION';
    }

    public function ask(StyleInterface $io, array $currentOptions): string
    {
        return $io->choice('Version of the mercure hub', ['v0', 'v1'], default: 'v0');
    }
}
