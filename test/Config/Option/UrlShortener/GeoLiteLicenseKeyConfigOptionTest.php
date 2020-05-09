<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\UrlShortener;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
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
    public function returnsExpectedConfig(): void
    {
        $this->assertEquals(['geolite2', 'license_key'], $this->configOption->getConfigPath());
    }

    /**
     * @test
     * @dataProvider provideAnswers
     */
    public function expectedQuestionIsAsked(?string $answer, string $expectedResult): void
    {
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask(
            'Provide a GeoLite2 license key. (Leave empty to use default one, but it is '
            . '<options=bold>strongly recommended</> to get your own. '
            . 'Go to https://shlink.io/documentation/geolite-license-key to know how to get it)',
        )->willReturn($answer);

        $result = $this->configOption->ask($io->reveal(), new PathCollection());

        $this->assertEquals($expectedResult, $result);
        $ask->shouldHaveBeenCalledOnce();
    }

    public function provideAnswers(): iterable
    {
        yield 'no answer' => [null, 'G4Lm0C60yJsnkdPi'];
        yield 'answer' => ['foo', 'foo'];
    }
}
