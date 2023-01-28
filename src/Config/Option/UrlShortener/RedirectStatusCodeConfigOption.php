<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\UrlShortener;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

use function array_flip;

class RedirectStatusCodeConfigOption extends BaseConfigOption
{
    public const ENV_VAR = 'REDIRECT_STATUS_CODE';
    private const REDIRECT_STATUSES = [
        302 => 'All visits will always be tracked. Not that good for SEO. Only GET requests will be redirected.',
        301 => 'Best option for SEO. Redirect will be cached for a short period of time, making some visits not to be '
            . 'tracked. Only GET requests will be redirected.',
        307 => 'Same as 302, but Shlink will also redirect on non-GET requests.',
        308 => 'Same as 301, but Shlink will also redirect on non-GET requests.',
    ];

    public function getEnvVar(): string
    {
        return self::ENV_VAR;
    }

    public function ask(StyleInterface $io, array $currentOptions): int
    {
        $options = self::REDIRECT_STATUSES;
        $answer = $io->choice('What kind of redirect do you want your short URLs to have?', $options, 302);

        return array_flip($options)[$answer];
    }
}
