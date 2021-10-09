<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Visit;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\Visit\OrphanVisitsWebhooksConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\Visit\VisitsWebhooksConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class OrphanVisitsWebhooksConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private OrphanVisitsWebhooksConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new OrphanVisitsWebhooksConfigOption(static fn () => true);
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        self::assertEquals(['url_shortener', 'notify_orphan_visits_to_webhooks'], $this->configOption->getConfigPath());
    }

    /** @test */
    public function returnsExpectedDependantOption(): void
    {
        self::assertEquals(VisitsWebhooksConfigOption::class, $this->configOption->getDependentOption());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = true;
        $io = $this->prophesize(StyleInterface::class);
        $confirm = $io->confirm(
            'Do you want to also notify the webhooks when an orphan visit occurs?',
            false,
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io->reveal(), new PathCollection());

        self::assertEquals($expectedAnswer, $answer);
        $confirm->shouldHaveBeenCalledOnce();
    }

    /**
     * @test
     * @dataProvider provideCurrentOptions
     */
    public function shouldBeAskedOnlyWhenTheListOfWebhooksIsNotEmpty(
        PathCollection $currentOptions,
        bool $expected,
    ): void {
        self::assertEquals($expected, $this->configOption->shouldBeAsked($currentOptions));
    }

    public function provideCurrentOptions(): iterable
    {
        yield 'without config' => [new PathCollection(), false];
        yield 'without webhooks' => [new PathCollection([
            'url_shortener' => [
                'visits_webhooks' => [],
            ],
        ]), false];
        yield 'with webhooks' => [new PathCollection([
            'url_shortener' => [
                'visits_webhooks' => ['foo', 'bar'],
            ],
        ]), true];
    }
}
