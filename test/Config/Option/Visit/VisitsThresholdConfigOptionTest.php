<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Visit;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
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
        self::assertEquals(['delete_short_urls', 'visits_threshold'], $this->configOption->getDeprecatedPath());
        self::assertEquals('DELETE_SHORT_URL_THRESHOLD', $this->configOption->getEnvVar());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 15;
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask(
            'What is the amount of visits from which the system will not allow short URLs to be deleted? Leave empty '
            . 'to always allow deleting short URLs, no matter what.',
            null,
            Argument::any(),
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io->reveal(), new PathCollection());

        self::assertEquals($expectedAnswer, $answer);
        $ask->shouldHaveBeenCalledOnce();
    }

    /**
     * @test
     * @dataProvider provideCurrentOptions
     */
    public function shouldBeCalledOnlyIfNotSetAndDriverIsNotSqlite(PathCollection $currentOptions, bool $expected): void
    {
        self::assertEquals($expected, $this->configOption->shouldBeAsked($currentOptions));
    }

    public function provideCurrentOptions(): iterable
    {
        $buildCollection = static function (bool $withThreshold): PathCollection {
            $collection = new PathCollection();
            if ($withThreshold) {
                $collection->setValueInPath(15, ['DELETE_SHORT_URL_THRESHOLD']);
            }

            return $collection;
        };

        yield 'without threshold' => [$buildCollection(false), true];
        yield 'with threshold' => [$buildCollection(true), false];
    }
}
