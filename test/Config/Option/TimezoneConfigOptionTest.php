<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\TimezoneConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class TimezoneConfigOptionTest extends TestCase
{
    private TimezoneConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new TimezoneConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('TIMEZONE', $this->configOption->getEnvVar());
    }

    #[Test, DataProvider('provideValidAnswers')]
    public function expectedQuestionIsAsked(string|null $answer, string $expectedAnswer): void
    {
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('ask')->with(
            'Set the timezone in which your Shlink instance is running (leave empty to use the one set in PHP config)',
        )->willReturn($answer);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }

    public static function provideValidAnswers(): iterable
    {
        yield ['the_answer', 'the_answer'];
        yield [null, ''];
    }

    #[Test, DataProvider('provideCurrentOptions')]
    public function shouldBeCalledOnlyIfItDoesNotYetExist(array $currentOptions, bool $expected): void
    {
        self::assertEquals($expected, $this->configOption->shouldBeAsked($currentOptions));
    }

    public static function provideCurrentOptions(): iterable
    {
        yield 'not exists in config' => [[], true];
        yield 'exists in config' => [['TIMEZONE' => 'America/Los_Angeles'], false];
    }
}
