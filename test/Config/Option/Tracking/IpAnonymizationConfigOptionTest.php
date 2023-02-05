<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Tracking;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Tracking\DisableIpTrackingConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\Tracking\DisableTrackingConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\Tracking\IpAnonymizationConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class IpAnonymizationConfigOptionTest extends TestCase
{
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
        $io = $this->createMock(StyleInterface::class);

        $io->expects($this->exactly($shouldWarn ? 2 : 1))->method('confirm')->willReturnMap([
            [
                'Do you want visitors\' remote IP addresses to be anonymized before persisting them to the database?',
                true,
                $firstAnswer,
            ],
            ['Do you still want to disable anonymization?', false, $secondAnswer],
        ]);
        $io->expects($this->exactly($shouldWarn ? 1 : 0))->method('warning')->with(
            'Careful! If you disable IP address anonymization, you will no longer be in compliance with the GDPR and '
            . 'other similar data protection regulations.',
        );

        $result = $this->configOption->ask($io, []);

        self::assertEquals($expectedResult, $result);
    }

    public static function provideConfirmAnswers(): iterable
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

    public static function provideCurrentConfig(): iterable
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
