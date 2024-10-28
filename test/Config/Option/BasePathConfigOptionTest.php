<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\BasePathConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class BasePathConfigOptionTest extends TestCase
{
    private BasePathConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new BasePathConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('BASE_PATH', $this->configOption->getEnvVar());
    }

    #[Test, DataProvider('provideValidAnswers')]
    public function expectedQuestionIsAsked(string|null $answer, string $expectedAnswer): void
    {
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('ask')->with(
            'What is the path from which shlink is going to be served? (It must include a leading bar, like "/shlink". '
            . 'Leave empty if you plan to serve shlink from the root of the domain)',
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
        yield 'exists in config' => [['BASE_PATH' => '/foo'], false];
    }
}
