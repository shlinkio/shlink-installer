<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Tracking;

use Override;
use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class DisableTrackingConfigOption extends BaseConfigOption
{
    public const string ENV_VAR = 'DISABLE_TRACKING';

    #[Override]
    public function getEnvVar(): string
    {
        return self::ENV_VAR;
    }

    #[Override]
    public function ask(StyleInterface $io, array $currentOptions): bool
    {
        return $io->confirm('Do you want to completely disable visits tracking?', false);
    }
}
