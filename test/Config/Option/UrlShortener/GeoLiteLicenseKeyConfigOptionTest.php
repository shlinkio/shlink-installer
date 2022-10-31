<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\UrlShortener;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\UrlShortener\GeoLiteLicenseKeyConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class GeoLiteLicenseKeyConfigOptionTest extends TestCase
{
    private GeoLiteLicenseKeyConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new GeoLiteLicenseKeyConfigOption();
    }

    /** @test */
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('GEOLITE_LICENSE_KEY', $this->configOption->getEnvVar());
    }

    /**
     * @test
     * @dataProvider provideAnswers
     */
    public function expectedQuestionIsAsked(?string $answer, ?string $expectedResult): void
    {
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('ask')->with(
            'Provide a GeoLite2 license key. Leave empty to disable geolocation. '
            . '(Go to https://shlink.io/documentation/geolite-license-key to know how to generate it)',
        )->willReturn($answer);

        $result = $this->configOption->ask($io, []);

        self::assertEquals($expectedResult, $result);
    }

    public function provideAnswers(): iterable
    {
        yield 'no answer' => [null, null];
        yield 'answer' => ['foo', 'foo'];
    }
}
