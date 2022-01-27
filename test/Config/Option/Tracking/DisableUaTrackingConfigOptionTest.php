<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Tracking;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\Tracking\DisableTrackingConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\Tracking\DisableUaTrackingConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class DisableUaTrackingConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private DisableUaTrackingConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new DisableUaTrackingConfigOption();
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        self::assertEquals(['tracking', 'disable_ua_tracking'], $this->configOption->getDeprecatedPath());
        self::assertEquals('DISABLE_UA_TRACKING', $this->configOption->getEnvVar());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = true;
        $io = $this->prophesize(StyleInterface::class);
        $confirm = $io->confirm('Do you want to disable tracking of visitors\' "User Agents"?', false)->willReturn(
            $expectedAnswer,
        );

        $answer = $this->configOption->ask($io->reveal(), new PathCollection());

        self::assertEquals($expectedAnswer, $answer);
        $confirm->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function getDependentOptionReturnsExpectedOption(): void
    {
        self::assertEquals(DisableTrackingConfigOption::class, $this->configOption->getDependentOption());
    }

    /**
     * @test
     * @dataProvider provideCurrentOptions
     */
    public function shouldBeAskedReturnsExpectedResultBasedOnCurrentOptions(
        PathCollection $currentOptions,
        bool $expected,
    ): void {
        self::assertEquals($expected, $this->configOption->shouldBeAsked($currentOptions));
    }

    public function provideCurrentOptions(): iterable
    {
        yield 'empty options' => [new PathCollection(), true];
        yield 'tracking not disabled' => [new PathCollection([
            DisableTrackingConfigOption::ENV_VAR => false,
        ]), true];
        yield 'tracking disabled' => [new PathCollection([
            DisableTrackingConfigOption::ENV_VAR => true,
        ]), false];
        yield 'option already set' => [new PathCollection([
            'DISABLE_UA_TRACKING' => false,
        ]), false];
        yield 'tracking not disabled with option already set' => [new PathCollection([
            DisableTrackingConfigOption::ENV_VAR => false,
            'DISABLE_UA_TRACKING' => false,
        ]), false];
    }
}
