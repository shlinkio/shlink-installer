<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Mercure;

use Shlinkio\Shlink\Installer\Config\Option\AbstractSwooleDependentConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class EnableMercureConfigOption extends AbstractSwooleDependentConfigOption
{
    public const ENV_VAR = 'MERCURE_ENABLED';

    public function getEnvVar(): string
    {
        return self::ENV_VAR;
    }

    public function ask(StyleInterface $io, array $currentOptions): bool
    {
        return $io->confirm('Do you want Shlink to publish real-time updates in a Mercure hub server?', false);
    }
}
