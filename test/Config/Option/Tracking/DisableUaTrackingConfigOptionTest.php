<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Tracking;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Tracking\DisableTrackingConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\Tracking\DisableUaTrackingConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class DisableUaTrackingConfigOptionTest extends TestCase
{
    private DisableUaTrackingConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new DisableUaTrackingConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('DISABLE_UA_TRACKING', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = true;
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('confirm')->with(
            'Do you want to disable tracking of visitors\' "User Agents"?',
            false,
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }

    #[Test]
    public function getDependentOptionReturnsExpectedOption(): void
    {
        self::assertEquals(DisableTrackingConfigOption::class, $this->configOption->getDependentOption());
    }

    #[Test, DataProvider('provideCurrentOptions')]
    public function shouldBeAskedReturnsExpectedResultBasedOnCurrentOptions(
        array $currentOptions,
        bool $expected,
    ): void {
        self::assertEquals($expected, $this->configOption->shouldBeAsked($currentOptions));
    }

    public static function provideCurrentOptions(): iterable
    {
        yield 'empty options' => [[], true];
        yield 'tracking not disabled' => [[DisableTrackingConfigOption::ENV_VAR => false], true];
        yield 'tracking disabled' => [[DisableTrackingConfigOption::ENV_VAR => true], false];
        yield 'option already set' => [['DISABLE_UA_TRACKING' => false], false];
        yield 'tracking not disabled with option already set' => [[
            DisableTrackingConfigOption::ENV_VAR => false,
            'DISABLE_UA_TRACKING' => false,
        ], false];
    }
}
