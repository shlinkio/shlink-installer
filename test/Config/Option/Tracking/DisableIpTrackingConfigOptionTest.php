<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Tracking;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\Tracking\DisableIpTrackingConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class DisableIpTrackingConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private DisableIpTrackingConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new DisableIpTrackingConfigOption();
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        self::assertEquals(['tracking', 'disable_ip_tracking'], $this->configOption->getConfigPath());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = true;
        $io = $this->prophesize(StyleInterface::class);
        $confirm = $io->confirm('Do you want to disable tracking of visitors\' IP addresses?', false)->willReturn(
            $expectedAnswer,
        );

        $answer = $this->configOption->ask($io->reveal(), new PathCollection());

        self::assertEquals($expectedAnswer, $answer);
        $confirm->shouldHaveBeenCalledOnce();
    }
}
