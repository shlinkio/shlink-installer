<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Installer\Config\Option\BasePathConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class BasePathConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private BasePathConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new BasePathConfigOption();
    }

    /** @test */
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('BASE_PATH', $this->configOption->getEnvVar());
    }

    /**
     * @test
     * @dataProvider provideValidAnswers
     */
    public function expectedQuestionIsAsked(?string $answer, string $expectedAnswer): void
    {
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask(
            'What is the path from which shlink is going to be served? (It must include a leading bar, like "/shlink". '
            . 'Leave empty if you plan to serve shlink from the root of the domain)',
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
        yield 'exists in config' => [['BASE_PATH' => '/foo'], false];
    }
}
