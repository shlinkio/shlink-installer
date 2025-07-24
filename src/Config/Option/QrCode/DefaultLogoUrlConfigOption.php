<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\QrCode;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidator;
use Symfony\Component\Console\Style\StyleInterface;

/** @deprecated Shlink has deprecated support for QR codes */
class DefaultLogoUrlConfigOption extends BaseConfigOption
{
    public function getEnvVar(): string
    {
        return 'DEFAULT_QR_CODE_LOGO_URL';
    }

    public function ask(StyleInterface $io, array $currentOptions): string|null
    {
        return $io->ask(
            'Provide a URL for a logo to be placed inside the QR code (leave empty to use no logo)',
            validator: ConfigOptionsValidator::validateUrl(...),
        );
    }
}
