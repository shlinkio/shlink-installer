<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Installer\Config\Option\TimezoneConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class TimezoneConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private TimezoneConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new TimezoneConfigOption();
    }

    /** @test */
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('TIMEZONE', $this->configOption->getEnvVar());
    }

    /**
     * @test
     * @dataProvider provideValidAnswers
     */
    public function expectedQuestionIsAsked(?string $answer, string $expectedAnswer): void
    {
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask(
            'Set the timezone in which your Shlink instance is running (leave empty to use the one set in PHP config)',
        )->willReturn($answer);

        $answer = $this->configOption->ask($io->reveal(), []);

        self::assertEquals($expectedAnswer, $answer);
        $ask->shouldHaveBeenCalledOnce();
    }

    public function provideValidAnswers(): iterable
    {
        yield ['the_answer', 'the_answer'];
        yield [null, ''];
    }

    /**
     * @test
     * @dataProvider provideCurrentOptions
     */
    public function shouldBeCalledOnlyIfItDoesNotYetExist(array $currentOptions, bool $expected): void
    {
        self::assertEquals($expected, $this->configOption->shouldBeAsked($currentOptions));
    }

    public function provideCurrentOptions(): iterable
    {
        yield 'not exists in config' => [[], true];
        yield 'exists in config' => [['TIMEZONE' => 'America/Los_Angeles'], false];
    }
}
