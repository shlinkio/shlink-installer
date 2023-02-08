<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\UrlShortener;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\UrlShortener\EnableMultiSegmentSlugsConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class EnableMultiSegmentSlugsConfigOptionTest extends TestCase
{
    private EnableMultiSegmentSlugsConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new EnableMultiSegmentSlugsConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('MULTI_SEGMENT_SLUGS_ENABLED', $this->configOption->getEnvVar());
    }

    #[Test, DataProvider('provideAnswers')]
    public function expectedQuestionIsAsked(string $providedAnswer, bool $expected): void
    {
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('choice')->with(
            'Do you want to support short URLs with multi-segment custom slugs? '
            . '(for example, https://example.com/foo/bar)',
            [
                'yes' => 'Custom slugs will support multiple segments (https://example.com/foo/bar/baz). Orphan '
                    . 'visits will only have either "base_url" or "invalid_short_url" type.',
                'no' => 'Slugs and short codes will support only one segment (https://example.com/foo). Orphan '
                    . 'visits will have one of "base_url", "invalid_short_url" or "regular_404" type.',
            ],
            'no',
        )->willReturn($providedAnswer);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expected, $answer);
    }

    public static function provideAnswers(): iterable
    {
        yield 'yes' => ['yes', true];
        yield 'no' => ['no', false];
    }
}
