<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\UrlShortener;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\UrlShortener\ShortCodeLengthOption;
use Symfony\Component\Console\Style\StyleInterface;

class ShortCodeLengthConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private ShortCodeLengthOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new ShortCodeLengthOption();
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        self::assertEquals(['url_shortener', 'default_short_codes_length'], $this->configOption->getConfigPath());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 5;
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask(
            'What is the default length you want generated short codes to have? (You will still be able to override '
            . 'this on every created short URL)',
            '5',
            Argument::any(),
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io->reveal(), new PathCollection());

        self::assertEquals($expectedAnswer, $answer);
        $ask->shouldHaveBeenCalledOnce();
    }
}
