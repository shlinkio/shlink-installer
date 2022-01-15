<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\QrCode;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidatorsTrait;
use Symfony\Component\Console\Style\StyleInterface;

class DefaultMarginConfigOption extends BaseConfigOption
{
    use ConfigOptionsValidatorsTrait;

    public function getDeprecatedPath(): array
    {
        return ['qr_codes', 'margin'];
    }

    public function getEnvVar(): string
    {
        return 'DEFAULT_QR_CODE_MARGIN';
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): int
    {
        return $io->ask(
            'What\'s the default margin, in pixels, you want generated QR codes to have',
            '0',
            fn (mixed $value) => $this->validateNumberGreaterThan($value, 0),
        );
    }
}
