<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\UrlShortener;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

use const PHP_EOL;

class ShortUrlModeConfigOption extends BaseConfigOption
{
    private const MODES = [
        'strict' => 'Short codes and custom slugs will be matched in a case-sensitive way ("foo" !== "FOO"). '
            . 'Generated short codes will include lowercase letters, uppercase letters and numbers.',
        'loosely' => 'Short codes and custom slugs will be matched in a case-insensitive way ("foo" === "FOO"). '
            . 'Generated short codes will include only lowercase letters and numbers.',
    ];

    public function getEnvVar(): string
    {
        return 'SHORT_URL_MODE';
    }

    public function ask(StyleInterface $io, array $currentOptions): string
    {
        $options = self::MODES;
        return $io->choice(
            'How do you want short URLs to be matched?'
            . PHP_EOL
            . '<options=bold;fg=yellow> Warning!</> <comment>This feature is experimental. It only applies to public '
            . 'routes (short URLs and QR codes). REST API routes always use strict match.</comment>'
            . PHP_EOL,
            $options,
            'strict',
        );
    }
}
