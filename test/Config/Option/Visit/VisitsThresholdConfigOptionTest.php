<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Visit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Visit\VisitsThresholdConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class VisitsThresholdConfigOptionTest extends TestCase
{
    private VisitsThresholdConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new VisitsThresholdConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('DELETE_SHORT_URL_THRESHOLD', $this->configOption->getEnvVar());
    }

    #[Test, DataProvider('provideValidAnswers')]
    public function expectedQuestionIsAsked(string|int|null $answer, int|null $expectedAnswer): void
    {
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('ask')->with(
            'What is the amount of visits from which the system will not allow short URLs to be deleted? Leave empty '
            . 'to always allow deleting short URLs, no matter what',
            null,
            $this->anything(),
        )->willReturn($answer);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }

    public static function provideValidAnswers(): iterable
    {
        yield [null, null];
        yield [10, 10];
        yield ['15', 15];
    }

    #[Test, DataProvider('provideCurrentOptions')]
    public function shouldBeCalledOnlyIfNotSetAndDriverIsNotSqlite(array $currentOptions, bool $expected): void
    {
        self::assertEquals($expected, $this->configOption->shouldBeAsked($currentOptions));
    }

    public static function provideCurrentOptions(): iterable
    {
        $buildCollection = static fn (bool $withThreshold): array =>
            $withThreshold ? ['DELETE_SHORT_URL_THRESHOLD' => 15] : [];

        yield 'without threshold' => [$buildCollection(false), true];
        yield 'with threshold' => [$buildCollection(true), false];
    }
}
