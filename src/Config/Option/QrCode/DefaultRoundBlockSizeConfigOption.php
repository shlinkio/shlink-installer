<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\QrCode;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class DefaultRoundBlockSizeConfigOption extends BaseConfigOption
{
    private const YES = 'yes';
    private const NO = 'no';

    public function getEnvVar(): string
    {
        return 'DEFAULT_QR_CODE_ROUND_BLOCK_SIZE';
    }

    public function ask(StyleInterface $io, array $currentOptions): bool
    {
        return $io->choice(
            'Do you want the QR codes block size to be rounded by default? QR codes could end up having some extra '
            . 'margin, but it will improve readability',
            [
                self::YES => 'Round block size, improving readability',
                self::NO => 'Do not round block size, preventing extra margin',
            ],
            self::YES,
        ) === self::YES;
    }
}
