<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\UrlShortener;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class EnableMultiSegmentSlugsConfigOption extends BaseConfigOption
{
    private const YES = 'yes';
    private const NO = 'no';

    public function getEnvVar(): string
    {
        return 'MULTI_SEGMENT_SLUGS_ENABLED';
    }

    public function ask(StyleInterface $io, array $currentOptions): bool
    {
        return $io->choice(
            'Do you want to support short URLs with multi-segment custom slugs? '
            . '(for example, https://example.com/foo/bar)',
            [
                self::YES =>
                    'Custom slugs will support multiple segments (https://example.com/foo/bar/baz). Orphan visits will '
                    . 'only have either "base_url" or "invalid_short_url" type.',
                self::NO => 'Slugs and short codes will support only one segment (https://example.com/foo). Orphan '
                    . 'visits will have one of "base_url", "invalid_short_url" or "regular_404" type.',
            ],
            self::NO,
        ) === self::YES;
    }
}
