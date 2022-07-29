<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\UrlShortener;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Installer\Config\Option\UrlShortener\GeoLiteLicenseKeyConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class GeoLiteLicenseKeyConfigOptionTest extends TestCase
{
    use ProphecyTrait;

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
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask(
            'Provide a GeoLite2 license key. Leave empty to disable geolocation. '
            . '(Go to https://shlink.io/documentation/geolite-license-key to know how to generate it)',
        )->willReturn($answer);

        $result = $this->configOption->ask($io->reveal(), []);

        self::assertEquals($expectedResult, $result);
        $ask->shouldHaveBeenCalledOnce();
    }

    public function provideAnswers(): iterable
    {
        yield 'no answer' => [null, null];
        yield 'answer' => ['foo', 'foo'];
    }
}
