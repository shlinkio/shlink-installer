<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\QrCode;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class DefaultErrorCorrectionConfigOption extends BaseConfigOption
{
    private const SUPPORTED_ERROR_CORRECTIONS = [
        'l' => 'Low',
        'm' => 'Medium',
        'q' => 'Quartile',
        'h' => 'High',
    ];

    public function getEnvVar(): string
    {
        return 'DEFAULT_QR_CODE_ERROR_CORRECTION';
    }

    public function ask(StyleInterface $io, array $currentOptions): string
    {
        return $io->choice(
            'What\'s the default error correction for generated QR codes',
            self::SUPPORTED_ERROR_CORRECTIONS,
            'l',
        );
    }
}
