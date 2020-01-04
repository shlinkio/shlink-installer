<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\ShortDomainSchemaConfigOption;
use Shlinkio\Shlink\Installer\Util\PathCollection;
use Symfony\Component\Console\Style\StyleInterface;

class ShortDomainSchemaConfigOptionTest extends TestCase
{
    private ShortDomainSchemaConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new ShortDomainSchemaConfigOption();
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        $this->assertEquals(['url_shortener', 'domain', 'schema'], $this->configOption->getConfigPath());
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

        $this->assertEquals($expectedAnswer, $answer);
        $choice->shouldHaveBeenCalledOnce();
    }
}
