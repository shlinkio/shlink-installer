<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Tracking;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Installer\Config\Option\Tracking\DisableIpTrackingConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\Tracking\DisableTrackingConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\Tracking\IpAnonymizationConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class IpAnonymizationConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private IpAnonymizationConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new IpAnonymizationConfigOption();
    }

    /** @test */
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('ANONYMIZE_REMOTE_ADDR', $this->configOption->getEnvVar());
    }

    /**
     * @test
     * @dataProvider provideConfirmAnswers
     */
    public function expectedQuestionIsAsked(
        bool $firstAnswer,
        bool $secondAnswer,
        bool $shouldWarn,
        bool $expectedResult,
    ): void {
        $io = $this->prophesize(StyleInterface::class);

        $firstConfirm = $io->confirm(
            'Do you want visitors\' remote IP addresses to be anonymized before persisting them to the database?',
        )->willReturn($firstAnswer);
        $secondConfirm = $io->confirm('Do you still want to disable anonymization?', false)->willReturn($secondAnswer);
        $warning = $io->warning(
            'Careful! If you disable IP address anonymization, you will no longer be in compliance with the GDPR and '
            . 'other similar data protection regulations.',
        );

        $result = $this->configOption->ask($io->reveal(), []);

        self::assertEquals($expectedResult, $result);
        $firstConfirm->shouldHaveBeenCalledOnce();
        $secondConfirm->shouldHaveBeenCalledTimes($shouldWarn ? 1 : 0);
        $warning->shouldHaveBeenCalledTimes($shouldWarn ? 1 : 0);
    }

    public function provideConfirmAnswers(): iterable
    {
        yield 'anonymizing' => [true, true, false, true];
        yield 'anonymizing 2' => [true, false, false, true];
        yield 'anonymizing after warning' => [false, false, true, true];
        yield 'not anonymizing' => [false, true, true, false];
    }

    /** @test */
    public function getDependentOptionReturnsExpectedResult(): void
    {
        self::assertEquals(DisableIpTrackingConfigOption::class, $this->configOption->getDependentOption());
    }

    /**
     * @test
     * @dataProvider provideCurrentConfig
     */
    public function shouldBeAskedIsTrueOnlyWhenAllConditionsAreMet(array $currentOptions, bool $expected): void
    {
        self::assertEquals($expected, $this->configOption->shouldBeAsked($currentOptions));
    }

    public function provideCurrentConfig(): iterable
    {
        yield [[], true];
        yield [[DisableTrackingConfigOption::ENV_VAR => false], true];
        yield [[DisableIpTrackingConfigOption::ENV_VAR => false], true];
        yield [[
            DisableTrackingConfigOption::ENV_VAR => false,
            DisableIpTrackingConfigOption::ENV_VAR => false,
        ], true];
        yield [[DisableTrackingConfigOption::ENV_VAR => true], false];
        yield [[DisableIpTrackingConfigOption::ENV_VAR => true], false];
        yield [['ANONYMIZE_REMOTE_ADDR' => true], false];
        yield [['ANONYMIZE_REMOTE_ADDR' => false], false];
    }
}
