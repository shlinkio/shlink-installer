<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Worker;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Installer\Config\Option\Worker\WebWorkerNumConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class WebWorkerNumConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private WebWorkerNumConfigOption $configOption;
    private bool $swooleInstalled;

    public function setUp(): void
    {
        $this->swooleInstalled = true;
        $this->configOption = new WebWorkerNumConfigOption(fn () => $this->swooleInstalled);
    }

    /** @test */
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('WEB_WORKER_NUM', $this->configOption->getEnvVar());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 16;
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask(
            'How many concurrent requests do you want Shlink to be able to serve? (Ignore this if you are '
            . 'not serving shlink with swoole or openswoole)',
            '16',
            Argument::any(),
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io->reveal(), []);

        self::assertEquals($expectedAnswer, $answer);
        $ask->shouldHaveBeenCalledOnce();
    }
}
