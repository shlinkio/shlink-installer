<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\QrCode;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class DefaultFormatConfigOption extends BaseConfigOption
{
    private const SUPPORTED_FORMATS = ['png', 'svg'];

    public function getDeprecatedPath(): array
    {
        return ['qr_codes', 'format'];
    }

    public function getEnvVar(): string
    {
        return 'DEFAULT_QR_CODE_FORMAT';
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): string
    {
        return $io->choice('What\'s the default format for generated QR codes', self::SUPPORTED_FORMATS, 'png');
    }
}
