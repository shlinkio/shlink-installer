<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\UrlShortener;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidatorsTrait;
use Symfony\Component\Console\Style\StyleInterface;

class ShortCodeLengthOption extends BaseConfigOption
{
    use ConfigOptionsValidatorsTrait;

    public function getDeprecatedPath(): array
    {
        return ['url_shortener', 'default_short_codes_length'];
    }

    public function getEnvVar(): string
    {
        return 'DEFAULT_SHORT_CODES_LENGTH';
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): int
    {
        return $io->ask(
            'What is the default length you want generated short codes to have? (You will still be able to override '
            . 'this on every created short URL)',
            '5',
            fn ($value) => $this->validateNumberGreaterThan($value, 4),
        );
    }
}
