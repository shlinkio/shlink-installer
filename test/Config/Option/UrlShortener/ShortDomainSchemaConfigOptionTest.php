<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\UrlShortener;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\UrlShortener\ShortDomainSchemaConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class ShortDomainSchemaConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private ShortDomainSchemaConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new ShortDomainSchemaConfigOption();
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        self::assertEquals(['url_shortener', 'domain', 'schema'], $this->configOption->getConfigPath());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 'https';
        $io = $this->prophesize(StyleInterface::class);
        $choice = $io->choice('Select schema for generated short URLs', ['http', 'https'], 'http')->willReturn(
            $expectedAnswer,
        );

        $answer = $this->configOption->ask($io->reveal(), new PathCollection());

        self::assertEquals($expectedAnswer, $answer);
        $choice->shouldHaveBeenCalledOnce();
    }
}
