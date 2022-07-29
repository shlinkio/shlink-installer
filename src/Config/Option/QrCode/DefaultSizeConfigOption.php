<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\QrCode;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidatorsTrait;
use Symfony\Component\Console\Style\StyleInterface;

class DefaultSizeConfigOption extends BaseConfigOption
{
    use ConfigOptionsValidatorsTrait;

    private const MIN_SIZE = 50;
    private const MAX_SIZE = 1000;

    public function getEnvVar(): string
    {
        return 'DEFAULT_QR_CODE_SIZE';
    }

    public function ask(StyleInterface $io, array $currentOptions): int
    {
        return $io->ask(
            'What\'s the default size, in pixels, you want generated QR codes to have (50 to 1000)',
            '300',
            fn (mixed $value) => $this->validateNumberBetween($value, self::MIN_SIZE, self::MAX_SIZE),
        );
    }
}
