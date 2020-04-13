<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\Visit\CheckVisitsThresholdConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\Visit\VisitsThresholdConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class VisitsThresholdConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private VisitsThresholdConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new VisitsThresholdConfigOption();
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        $this->assertEquals(['delete_short_urls', 'visits_threshold'], $this->configOption->getConfigPath());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 15;
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask(
            'What is the amount of visits from which the system will not allow short URLs to be deleted?',
            '15',
            Argument::any(),
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io->reveal(), new PathCollection());

        $this->assertEquals($expectedAnswer, $answer);
        $ask->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function dependsOnCheck(): void
    {
        $this->assertEquals(CheckVisitsThresholdConfigOption::class, $this->configOption->getDependentOption());
    }

    /**
     * @test
     * @dataProvider provideCurrentOptions
     */
    public function shouldBeCalledOnlyIfNotSetAndDriverIsNotSqlite(PathCollection $currentOptions, bool $expected): void
    {
        $this->assertEquals($expected, $this->configOption->shouldBeAsked($currentOptions));
    }

    public function provideCurrentOptions(): iterable
    {
        $buildCollection = static function (bool $check, bool $withThreshold = false): PathCollection {
            $collection = new PathCollection();
            $collection->setValueInPath($check, CheckVisitsThresholdConfigOption::CONFIG_PATH);
            if ($withThreshold) {
                $collection->setValueInPath(15, ['delete_short_urls', 'visits_threshold']);
            }

            return $collection;
        };

        yield 'none' => [$buildCollection(false), false];
        yield 'with check' => [$buildCollection(true), true];
        yield 'with check and threshold' => [$buildCollection(true, true), false];
    }
}
