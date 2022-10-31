<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Visit;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
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
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('NOTIFY_ORPHAN_VISITS_TO_WEBHOOKS', $this->configOption->getEnvVar());
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
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('confirm')->with(
            'Do you want to also notify the webhooks when an orphan visit occurs?',
            false,
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }

    /**
     * @test
     * @dataProvider provideCurrentOptions
     */
    public function shouldBeAskedOnlyWhenTheListOfWebhooksIsNotEmpty(
        array $currentOptions,
        bool $expected,
    ): void {
        self::assertEquals($expected, $this->configOption->shouldBeAsked($currentOptions));
    }

    public function provideCurrentOptions(): iterable
    {
        yield 'without config' => [[], false];
        yield 'without webhooks' => [[VisitsWebhooksConfigOption::ENV_VAR => []], false];
        yield 'with webhooks' => [[VisitsWebhooksConfigOption::ENV_VAR => ['foo', 'bar']], true];
    }
}
