<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Geolocation;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Geolocation\GeolocationStrategiesConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class GeolocationStrategiesConfigOptionTest extends TestCase
{
    private GeolocationStrategiesConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new GeolocationStrategiesConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('GEOLOCATION_STRATEGIES', $this->configOption->getEnvVar());
    }

    #[Test]
    #[TestWith([true, false, 'geolite2'])]
    #[TestWith([false, true, 'cloudflare'])]
    public function oneStrategyIsReturnedWhenTheOtherIsRejected(
        bool $firstConfirm,
        bool $secondConfirm,
        string $expectedResult,
    ): void {
        $io = $this->createMock(StyleInterface::class);
        $io
            ->expects($this->exactly(2))
            ->method('confirm')
            ->willReturnMap([
                ['Do you want to geolocate visitors using a GeoLite2 database?',         $firstConfirm],
                ['Do you want to geolocate visitors using Cloudflare-provided headers?', $secondConfirm],
            ]);
        $io->expects($this->never())->method('choice');

        $result = $this->configOption->ask($io, []);

        self::assertEquals($expectedResult, $result);
    }

    #[Test]
    #[TestWith(['GeoLite2', 'geolite2,cloudflare'])]
    #[TestWith(['Cloudflare', 'cloudflare,geolite2'])]
    public function orderIsRequestedWhenBothStrategiesAreChosen(string $firstChoice, string $expectedResult): void
    {
        $io = $this->createMock(StyleInterface::class);
        $io
            ->expects($this->exactly(2))
            ->method('confirm')
            ->willReturnMap([
                ['Do you want to geolocate visitors using a GeoLite2 database?',         true],
                ['Do you want to geolocate visitors using Cloudflare-provided headers?', true],
            ]);
        $io->expects($this->once())->method('choice')->willReturn($firstChoice);

        $result = $this->configOption->ask($io, []);

        self::assertEquals($expectedResult, $result);
    }
}
