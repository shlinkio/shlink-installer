<?php

namespace ShlinkioTest\Shlink\Installer\Config\Option\RealTimeUpdates;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\RealTimeUpdates\RealTimeUpdatesTopicsConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class RealTimeUpdatesTopicsConfigOptionTest extends TestCase
{
    private RealTimeUpdatesTopicsConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new RealTimeUpdatesTopicsConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('REAL_TIME_UPDATES_TOPICS', $this->configOption->getEnvVar());
    }

    /**
     * @param list<bool> $topicAnswers
     */
    #[Test]
    #[TestWith([false, [], null])]
    #[TestWith([true, [false, false, false, false], []])]
    #[TestWith([true, [true, false, false, false], ['NEW_VISIT']])]
    #[TestWith([true, [true, false, false, true], ['NEW_VISIT', 'NEW_SHORT_URL']])]
    #[TestWith([true, [false, true, true, true], ['NEW_SHORT_URL_VISIT', 'NEW_ORPHAN_VISIT', 'NEW_SHORT_URL']])]
    public function expectedQuestionIsAsked(
        bool $individualTopicsAnswer,
        array $topicAnswers,
        array|null $expectedTopics,
    ): void {
        $io = $this->createMock(StyleInterface::class);
        $io->method('confirm')->willReturnOnConsecutiveCalls($individualTopicsAnswer, ...$topicAnswers);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedTopics, $answer);
    }
}
