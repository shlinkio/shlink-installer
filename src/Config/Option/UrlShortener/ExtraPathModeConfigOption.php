<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\UrlShortener;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class ExtraPathModeConfigOption extends BaseConfigOption
{
    public const array MODES = [
        'default' => 'Match strictly',
        'append' => 'Append extra path',
        'ignore' => 'Discard extra path',
    ];

    public function getEnvVar(): string
    {
        return 'REDIRECT_EXTRA_PATH_MODE';
    }

    public function ask(StyleInterface $io, array $currentOptions): string
    {
        return $io->choice(
            question: <<<QUESTION
            Do you want Shlink to redirect short URLs as soon as the first segment of the path matches a short code?

              append:
                * {shortDomain}/{shortCode}/[...extraPath] -> {longUrl}/[...extraPath]
                * https://s.test/abc123                    -> https://www.example.com
                * https://s.test/abc123/shlinkio           -> https://www.example.com/shlinkio

              ignore:
                * {shortDomain}/{shortCode}/[...extraPath] -> {longUrl}
                * https://s.test/abc123                    -> https://www.example.com
                * https://s.test/abc123/shlinkio           -> https://www.example.com


            QUESTION,
            choices: self::MODES,
            default: 'default',
        );
    }
}
