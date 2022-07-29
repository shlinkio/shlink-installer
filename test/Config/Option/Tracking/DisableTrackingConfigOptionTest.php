<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Tracking;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Installer\Config\Option\Tracking\DisableTrackingConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class DisableTrackingConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private DisableTrackingConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new DisableTrackingConfigOption();
    }

    /** @test */
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('DISABLE_TRACKING', $this->configOption->getEnvVar());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = true;
        $io = $this->prophesize(StyleInterface::class);
        $confirm = $io->confirm('Do you want to completely disable visits tracking?', false)->willReturn(
            $expectedAnswer,
        );

        $answer = $this->configOption->ask($io->reveal(), []);

        self::assertEquals($expectedAnswer, $answer);
        $confirm->shouldHaveBeenCalledOnce();
    }
}
