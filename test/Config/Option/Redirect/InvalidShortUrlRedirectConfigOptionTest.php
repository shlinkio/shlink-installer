<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Redirect;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Redirect\InvalidShortUrlRedirectConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class InvalidShortUrlRedirectConfigOptionTest extends TestCase
{
    private InvalidShortUrlRedirectConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new InvalidShortUrlRedirectConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('DEFAULT_INVALID_SHORT_URL_REDIRECT', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 'the_answer';
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('ask')->with(
            'Custom URL to redirect to when a user hits an invalid short URL (If no value is provided, the '
            . 'user will see a default "404 not found" page)',
            null,
            $this->anything(),
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }
}
