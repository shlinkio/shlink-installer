<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Worker;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Worker\TaskWorkerNumConfigOption;
use Shlinkio\Shlink\Installer\Exception\InvalidConfigOptionException;
use Symfony\Component\Console\Style\StyleInterface;
use Throwable;

class TaskWorkerNumConfigOptionTest extends TestCase
{
    private TaskWorkerNumConfigOption $configOption;
    private bool $swooleInstalled;

    public function setUp(): void
    {
        $this->swooleInstalled = true;
        $this->configOption = new TaskWorkerNumConfigOption(fn () => $this->swooleInstalled);
    }

    /** @test */
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('TASK_WORKER_NUM', $this->configOption->getEnvVar());
    }

    /**
     * @test
     * @dataProvider provideValidValues
     */
    public function expectedQuestionIsAsked(int $expectedAnswer): void
    {
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('ask')->with(
            'How many concurrent background tasks do you want Shlink to be able to execute? (Ignore this if you are '
            . 'not serving shlink with swoole or openswoole)',
            '16',
            $this->callback(function (callable $arg) use ($expectedAnswer) {
                $arg($expectedAnswer);
                return true;
            }),
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }

    public function provideValidValues(): iterable
    {
        yield [4];
        yield [10];
        yield [16];
    }

    /**
     * @test
     * @dataProvider provideInvalidValues
     */
    public function throwsAnErrorWhenProvidedValueDoesNotMeetTheMinimum(
        mixed $expectedAnswer,
        string $expectedMessage,
    ): void {
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('ask')->with(
            'How many concurrent background tasks do you want Shlink to be able to execute? (Ignore this if you are '
            . 'not serving shlink with swoole or openswoole)',
            '16',
            $this->callback(function (callable $arg) use ($expectedAnswer, $expectedMessage) {
                try {
                    $arg($expectedAnswer);
                } catch (Throwable $e) {
                    Assert::assertInstanceOf(InvalidConfigOptionException::class, $e);
                    Assert::assertEquals($expectedMessage, $e->getMessage());
                }

                return true;
            }),
        )->willReturn(1);

        $this->configOption->ask($io, []);
    }

    public function provideInvalidValues(): iterable
    {
        yield '3' => [3, 'Provided value "3" is invalid. Expected a number greater or equal than 4'];
        yield '2' => [2, 'Provided value "2" is invalid. Expected a number greater or equal than 4'];
        yield '1' => [1, 'Provided value "1" is invalid. Expected a number greater or equal than 4'];
        yield 'negative' => [-10, 'Provided value "-10" is invalid. Expected a number greater or equal than 4'];
        yield 'string' => [
            'not a number',
            'Provided value "not a number" is invalid. Expected a number greater or equal than 4',
        ];
    }

    /**
     * @test
     * @dataProvider provideCurrentOptions
     */
    public function shouldBeAskedWhenNotPresentAndSwooleIsInstalled(
        bool $swooleInstalled,
        array $currentOptions,
        bool $expected,
    ): void {
        $this->swooleInstalled = $swooleInstalled;
        self::assertEquals($expected, $this->configOption->shouldBeAsked($currentOptions));
    }

    public function provideCurrentOptions(): iterable
    {
        yield 'without swoole' => [false, [], false];
        yield 'with swoole and no config' => [true, [], true];
        yield 'with swoole and config' => [true, ['TASK_WORKER_NUM' => 16], false];
    }
}
