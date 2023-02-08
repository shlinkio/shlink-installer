<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Tracking;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Tracking\DisableIpTrackingConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class DisableIpTrackingConfigOptionTest extends TestCase
{
    private DisableIpTrackingConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new DisableIpTrackingConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('DISABLE_IP_TRACKING', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = true;
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('confirm')->with(
            'Do you want to disable tracking of visitors\' IP addresses?',
            false,
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }
}
