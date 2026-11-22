<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Robots;

use Override;
use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class RobotsAllowAllShortUrlsConfigOption extends BaseConfigOption
{
    #[Override]
    public function getEnvVar(): string
    {
        return 'ROBOTS_ALLOW_ALL_SHORT_URLS';
    }

    #[Override]
    public function ask(StyleInterface $io, array $currentOptions): bool
    {
        return $io->confirm(
            'Do you want all short URLs to be crawlable/allowed by the robots.txt file? '
            . 'You can still allow them individually, regardless of this.',
            default: false,
        );
    }
}
