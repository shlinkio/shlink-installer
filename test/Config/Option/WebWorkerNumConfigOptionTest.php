<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Shlinkio\Shlink\Installer\Config\Option\WebWorkerNumConfigOption;
use Shlinkio\Shlink\Installer\Util\PathCollection;
use Symfony\Component\Console\Style\StyleInterface;

class WebWorkerNumConfigOptionTest extends TestCase
{
    private WebWorkerNumConfigOption $configOption;
    private bool $swooleInstalled;

    public function setUp(): void
    {
        $this->swooleInstalled = true;
        $this->configOption = new WebWorkerNumConfigOption(fn () => $this->swooleInstalled);
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        $this->assertEquals(['web_worker_num'], $this->configOption->getConfigPath());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 16;
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask(
            'How many concurrent requests do you want Shlink to be able to serve? (Ignore this if you are '
            . 'not serving shlink with swoole)',
            '16',
            Argument::any(),
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io->reveal(), new PathCollection());

        $this->assertEquals($expectedAnswer, $answer);
        $ask->shouldHaveBeenCalledOnce();
    }
}
