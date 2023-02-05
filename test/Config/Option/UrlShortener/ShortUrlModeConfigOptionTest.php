<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\UrlShortener;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
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

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('SHORT_URL_MODE', $this->configOption->getEnvVar());
    }

    #[Test, DataProvider('provideChoices')]
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

    public static function provideChoices(): iterable
    {
        yield 'strict' => ['strict'];
        yield 'loose' => ['loose'];
    }

    #[Test, DataProvider('provideMigrationValues')]
    public function deprecatedValueIsProperlyMigrated(string $existingValue, string $expectedResult): void
    {
        self::assertEquals($expectedResult, $this->configOption->tryToMigrateValue($existingValue));
    }

    public static function provideMigrationValues(): iterable
    {
        yield 'strict' => ['strict', 'strict'];
        yield 'loose' => ['loose', 'loose'];
        yield 'loosely' => ['loosely', 'loose'];
    }
}
