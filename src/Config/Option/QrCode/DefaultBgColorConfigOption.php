<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\QrCode;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidator;
use Symfony\Component\Console\Style\StyleInterface;

/** @deprecated Shlink has deprecated support for QR codes */
class DefaultBgColorConfigOption extends BaseConfigOption
{
    public function getEnvVar(): string
    {
        return 'DEFAULT_QR_CODE_BG_COLOR';
    }

    public function ask(StyleInterface $io, array $currentOptions): string|null
    {
        return $io->ask(
            'What\'s the default background color for generated QR codes',
            '#FFFFFF',
            ConfigOptionsValidator::validateHexColor(...),
        );
    }
}
