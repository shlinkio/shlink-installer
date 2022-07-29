<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Worker;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Installer\Config\Option\Worker\TaskWorkerNumConfigOption;
use Shlinkio\Shlink\Installer\Exception\InvalidConfigOptionException;
use Symfony\Component\Console\Style\StyleInterface;

class TaskWorkerNumConfigOptionTest extends TestCase
{
    use ProphecyTrait;

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
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask(
            'How many concurrent background tasks do you want Shlink to be able to execute? (Ignore this if you are '
            . 'not serving shlink with swoole or openswoole)',
            '16',
            Argument::that(fn (callable $arg) => $arg($expectedAnswer)),
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io->reveal(), []);

        self::assertEquals($expectedAnswer, $answer);
        $ask->shouldHaveBeenCalledOnce();
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
    public function throwsAnErrorWHenProvidedValueDoesNotMeetTheMinimum(
        mixed $expectedAnswer,
        string $expectedMessage,
    ): void {
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask(
            'How many concurrent background tasks do you want Shlink to be able to execute? (Ignore this if you are '
            . 'not serving shlink with swoole or openswoole)',
            '16',
            Argument::that(fn (callable $arg) => $arg($expectedAnswer)),
        )->willReturn($expectedAnswer);

        $this->expectException(InvalidConfigOptionException::class);
        $this->expectExceptionMessage($expectedMessage);

        $this->configOption->ask($io->reveal(), []);
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
