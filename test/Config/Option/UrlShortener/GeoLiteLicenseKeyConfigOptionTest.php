<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\UrlShortener;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Geolocation\GeolocationStrategiesConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\UrlShortener\GeoLiteLicenseKeyConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class GeoLiteLicenseKeyConfigOptionTest extends TestCase
{
    private GeoLiteLicenseKeyConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new GeoLiteLicenseKeyConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('GEOLITE_LICENSE_KEY', $this->configOption->getEnvVar());
    }

    #[Test, DataProvider('provideAnswers')]
    public function expectedQuestionIsAsked(string|null $answer, string|null $expectedResult): void
    {
        $io = $this->createMock(StyleInterface::class);
        $io
            ->expects($this->once())
            ->method('ask')
            ->with(
                'Provide a GeoLite2 license key. Leave empty to disable geolocation with GeoLite2. '
                . '(Go to https://shlink.io/documentation/geolite-license-key to know how to generate it)',
            )
            ->willReturn($answer);

        $result = $this->configOption->ask($io, [GeolocationStrategiesConfigOption::ENV_VAR => 'geolite2']);

        self::assertEquals($expectedResult, $result);
    }

    public static function provideAnswers(): iterable
    {
        yield 'no answer' => [null, null];
        yield 'answer' => ['foo', 'foo'];
    }

    #[Test]
    #[TestWith([[GeolocationStrategiesConfigOption::ENV_VAR => 'cloudflare']])]
    #[TestWith([[]])]
    public function questionIsSkippedIfGeoliteIsNotBetweenSelectedStrategies(array $currentOptions): void
    {
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->never())->method('ask');

        $result = $this->configOption->ask($io, $currentOptions);

        self::assertNull($result);
    }
}
