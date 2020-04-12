<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\CheckVisitsThresholdConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class CheckVisitsThresholdConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private CheckVisitsThresholdConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new CheckVisitsThresholdConfigOption();
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        $this->assertEquals(['delete_short_urls', 'check_visits_threshold'], $this->configOption->getConfigPath());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = true;
        $io = $this->prophesize(StyleInterface::class);
        $confirm = $io->confirm(
            'Do you want to enable a safety check which will not allow short URLs to be deleted after receiving '
            . 'a specific amount of visits?',
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io->reveal(), new PathCollection());

        $this->assertEquals($expectedAnswer, $answer);
        $confirm->shouldHaveBeenCalledOnce();
    }
}
