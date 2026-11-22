<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Tracking;

use Override;
use Symfony\Component\Console\Style\StyleInterface;

class DisableIpTrackingConfigOption extends AbstractDisableTrackingDependentConfigOption
{
    public const string ENV_VAR = 'DISABLE_IP_TRACKING';

    #[Override]
    public function getEnvVar(): string
    {
        return self::ENV_VAR;
    }

    #[Override]
    public function ask(StyleInterface $io, array $currentOptions): bool
    {
        return $io->confirm('Do you want to disable tracking of visitors\' IP addresses?', false);
    }
}
