<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Visit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Server\RuntimeConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\Visit\OrphanVisitsWebhooksConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\Visit\VisitsWebhooksConfigOption;
use Shlinkio\Shlink\Installer\Config\Util\RuntimeType;
use Symfony\Component\Console\Style\StyleInterface;

class OrphanVisitsWebhooksConfigOptionTest extends TestCase
{
    private OrphanVisitsWebhooksConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new OrphanVisitsWebhooksConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('NOTIFY_ORPHAN_VISITS_TO_WEBHOOKS', $this->configOption->getEnvVar());
    }

    #[Test]
    public function returnsExpectedDependantOption(): void
    {
        self::assertEquals(VisitsWebhooksConfigOption::class, $this->configOption->getDependentOption());
    }

    #[Test]
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

    #[Test, DataProvider('provideCurrentOptions')]
    public function shouldBeAskedOnlyWhenTheListOfWebhooksIsNotEmpty(
        array $currentOptions,
        bool $expected,
    ): void {
        self::assertEquals($expected, $this->configOption->shouldBeAsked([
            RuntimeConfigOption::ENV_VAR => RuntimeType::ASYNC->value,
            ...$currentOptions,
        ]));
    }

    public static function provideCurrentOptions(): iterable
    {
        yield 'without config' => [[], false];
        yield 'without webhooks' => [[VisitsWebhooksConfigOption::ENV_VAR => []], false];
        yield 'with webhooks' => [[VisitsWebhooksConfigOption::ENV_VAR => ['foo', 'bar']], true];
    }
}
