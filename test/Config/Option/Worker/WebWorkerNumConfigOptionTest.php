<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Worker;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Worker\WebWorkerNumConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class WebWorkerNumConfigOptionTest extends TestCase
{
    private WebWorkerNumConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new WebWorkerNumConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('WEB_WORKER_NUM', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 16;
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('ask')->with(
            'How many concurrent requests do you want Shlink to be able to serve?',
            '16',
            $this->anything(),
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }
}
