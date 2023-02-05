<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\UrlShortener;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\UrlShortener\ShortUrlModeConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

use const PHP_EOL;

class ShortUrlModeConfigOptionTest extends TestCase
{
    private ShortUrlModeConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new ShortUrlModeConfigOption();
    }

    /** @test */
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('SHORT_URL_MODE', $this->configOption->getEnvVar());
    }

    /**
     * @test
     * @dataProvider provideChoices
     */
    public function expectedQuestionIsAsked(string $choice): void
    {
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('choice')->with(
            'How do you want short URLs to be matched?'
            . PHP_EOL
            . '<options=bold;fg=yellow> Warning!</> <comment>This feature is experimental. It only applies to public '
            . 'routes (short URLs and QR codes). REST API routes always use strict match.</comment>'
            . PHP_EOL,
            $this->isType('array'),
            'strict',
        )->willReturn($choice);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($choice, $answer);
    }

    public function provideChoices(): iterable
    {
        yield 'strict' => ['strict'];
        yield 'loose' => ['loose'];
    }
}
