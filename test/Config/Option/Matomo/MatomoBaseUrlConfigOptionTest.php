<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Matomo;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Matomo\MatomoBaseUrlConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\Matomo\MatomoEnabledConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class MatomoBaseUrlConfigOptionTest extends TestCase
{
    private MatomoBaseUrlConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new MatomoBaseUrlConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('MATOMO_BASE_URL', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 'foobar.com';
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('ask')->with('Matomo server URL')->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }

    #[Test]
    public function dependsOnMatomoEnabled(): void
    {
        self::assertEquals(MatomoEnabledConfigOption::class, $this->configOption->getDependentOption());
    }

    #[Test, DataProvider('provideCurrentOptions')]
    public function shouldBeAskedOnlyIfMatomoIsEnabled(array $currentOptions, bool $expected): void
    {
        self::assertEquals($expected, $this->configOption->shouldBeAsked($currentOptions));
    }

    public static function provideCurrentOptions(): iterable
    {
        yield 'matomo enabled' => [[MatomoEnabledConfigOption::ENV_VAR => true], true];
        yield 'matomo not enabled' => [[MatomoEnabledConfigOption::ENV_VAR => false], false];
        yield 'matomo not set' => [[], false];
    }
}
