<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\UrlShortener;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\UrlShortener\ExtraPathModeConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class ExtraPathModeConfigOptionTest extends TestCase
{
    private ExtraPathModeConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new ExtraPathModeConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('REDIRECT_EXTRA_PATH_MODE', $this->configOption->getEnvVar());
    }

    #[Test, DataProvider('provideChoices')]
    public function expectedQuestionIsAsked(string $choice): void
    {
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('choice')->with(
            <<<QUESTION
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
            ExtraPathModeConfigOption::MODES,
            'default',
        )->willReturn($choice);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($choice, $answer);
    }

    public static function provideChoices(): iterable
    {
        foreach (ExtraPathModeConfigOption::MODES as $mode => $_) {
            yield $mode => [$mode];
        }
    }
}
