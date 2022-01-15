<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\UrlShortener;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

use function array_flip;

class RedirectStatusCodeConfigOption extends BaseConfigOption
{
    public const ENV_VAR = 'REDIRECT_STATUS_CODE';
    public const CONFIG_PATH = [self::ENV_VAR];
    private const REDIRECT_STATUSES = [
        'All visits will always be tracked. Not that good for SEO.' => 302,
        'Best option for SEO. Redirect will be cached for a short period of time, making some visits not to be tracked.' => 301, // phpcs:ignore
    ];

    public function getDeprecatedPath(): array
    {
        return ['url_shortener', 'redirect_status_code'];
    }

    public function getEnvVar(): string
    {
        return self::ENV_VAR;
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): int
    {
        $options = array_flip(self::REDIRECT_STATUSES);
        $answer = $io->choice('What kind of redirect do you want your short URLs to have?', $options, $options[302]);
        return self::REDIRECT_STATUSES[$answer];
    }
}
