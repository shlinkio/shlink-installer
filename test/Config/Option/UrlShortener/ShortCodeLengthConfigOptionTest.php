<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\UrlShortener;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\UrlShortener\ShortCodeLengthOption;
use Symfony\Component\Console\Style\StyleInterface;

class ShortCodeLengthConfigOptionTest extends TestCase
{
    private ShortCodeLengthOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new ShortCodeLengthOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('DEFAULT_SHORT_CODES_LENGTH', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 5;
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('ask')->with(
            'What is the default length you want generated short codes to have? (You will still be able to override '
            . 'this on every created short URL)',
            '5',
            $this->anything(),
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }
}
