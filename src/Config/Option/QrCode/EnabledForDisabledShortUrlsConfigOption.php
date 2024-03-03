<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\QrCode;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class EnabledForDisabledShortUrlsConfigOption extends BaseConfigOption
{
    public function getEnvVar(): string
    {
        return 'QR_CODE_FOR_DISABLED_SHORT_URLS';
    }

    public function ask(StyleInterface $io, array $currentOptions): bool
    {
        return $io->confirm(
            'Should Shlink be able to generate QR codes for short URLs which are not enabled? (Short URLs are not '
            . 'enabled if they have a "valid since" in the future, a "valid until" in the past, or reached the maximum '
            . 'amount of allowed visits)',
        );
    }
}
