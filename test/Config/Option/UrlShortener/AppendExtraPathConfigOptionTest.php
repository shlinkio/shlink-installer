<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\UrlShortener;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\UrlShortener\AppendExtraPathConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class AppendExtraPathConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private AppendExtraPathConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new AppendExtraPathConfigOption();
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        self::assertEquals(['url_shortener', 'append_extra_path'], $this->configOption->getDeprecatedPath());
        self::assertEquals('REDIRECT_APPEND_EXTRA_PATH', $this->configOption->getEnvVar());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = true;
        $io = $this->prophesize(StyleInterface::class);
        $confirm = $io->confirm(
        //@codingStandardsIgnoreStart
            <<<FOO
            Do you want Shlink to redirect short URLs as soon as the first segment of the path matches a short code, appending the rest to the long URL?
               * {shortDomain}/{shortCode}/[...extraPath] -> {longUrl}/[...extraPath]
               * https://example.com/abc123               -> https://www.twitter.com
               * https://example.com/abc123/shlinkio      -> https://www.twitter.com/shlinkio
               
            FOO,
            //@codingStandardsIgnoreEnd
            false,
        )->willReturn(
            $expectedAnswer,
        );

        $answer = $this->configOption->ask($io->reveal(), new PathCollection());

        self::assertEquals($expectedAnswer, $answer);
        $confirm->shouldHaveBeenCalledOnce();
    }
}
