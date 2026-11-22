<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Mercure;

use Override;
use Symfony\Component\Console\Style\StyleInterface;

class MercureVersionConfigOption extends AbstractMercureEnabledConfigOption
{
    public const string ENV_VAR = 'MERCURE_VERSION';

    #[Override]
    public function getEnvVar(): string
    {
        return self::ENV_VAR;
    }

    #[Override]
    public function ask(StyleInterface $io, array $currentOptions): string
    {
        return $io->choice('Version of the mercure hub', ['legacy', 'stable'], default: 'legacy');
    }
}
