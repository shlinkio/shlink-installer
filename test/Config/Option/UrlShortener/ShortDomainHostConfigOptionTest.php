<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\UrlShortener;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\UrlShortener\ShortDomainHostConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class ShortDomainHostConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private ShortDomainHostConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new ShortDomainHostConfigOption();
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        $this->assertEquals(['url_shortener', 'domain', 'hostname'], $this->configOption->getConfigPath());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 'the_answer';
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask('Default domain for generated short URLs', null, Argument::any())->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io->reveal(), new PathCollection());

        $this->assertEquals($expectedAnswer, $answer);
        $ask->shouldHaveBeenCalledOnce();
    }
}
