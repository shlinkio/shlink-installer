<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Tracking;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Installer\Config\Option\Tracking\OrphanVisitsTrackingConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class OrphanVisitsTrackingConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private OrphanVisitsTrackingConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new OrphanVisitsTrackingConfigOption();
    }

    /** @test */
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('TRACK_ORPHAN_VISITS', $this->configOption->getEnvVar());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = true;
        $io = $this->prophesize(StyleInterface::class);
        $confirm = $io->confirm(
            'Do you want track orphan visits? (visits to the base URL, invalid short URLs or other "not found" URLs)',
            true,
        )->willReturn(
            $expectedAnswer,
        );

        $answer = $this->configOption->ask($io->reveal(), []);

        self::assertEquals($expectedAnswer, $answer);
        $confirm->shouldHaveBeenCalledOnce();
    }
}
