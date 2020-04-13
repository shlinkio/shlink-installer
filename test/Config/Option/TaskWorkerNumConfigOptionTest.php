<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\Worker\TaskWorkerNumConfigOption;
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
    public function returnsExpectedConfig(): void
    {
        $this->assertEquals(['task_worker_num'], $this->configOption->getConfigPath());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 16;
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask(
            'How many concurrent background tasks do you want Shlink to be able to execute? (Ignore this if you are '
            . 'not serving shlink with swoole)',
            '16',
            Argument::any(),
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io->reveal(), new PathCollection());

        $this->assertEquals($expectedAnswer, $answer);
        $ask->shouldHaveBeenCalledOnce();
    }

    /**
     * @test
     * @dataProvider provideCurrentOptions
     */
    public function shouldBeAskedWhenNotPresentAndSwooleIsInstalled(
        bool $swooleInstalled,
        PathCollection $currentOptions,
        bool $expected
    ): void {
        $this->swooleInstalled = $swooleInstalled;
        $this->assertEquals($expected, $this->configOption->shouldBeAsked($currentOptions));
    }

    public function provideCurrentOptions(): iterable
    {
        yield 'without swoole' => [false, new PathCollection(), false];
        yield 'with swoole and no config' => [true, new PathCollection(), true];
        yield 'with swoole and config' => [true, new PathCollection(['task_worker_num' => 16]), false];
    }
}
