<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Mercure;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\Mercure\EnableMercureConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class EnableMercureConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private EnableMercureConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new EnableMercureConfigOption(fn () => false);
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        self::assertEquals(['___', 'mercure_enabled'], $this->configOption->getConfigPath());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = true;
        $io = $this->prophesize(StyleInterface::class);
        $confirm = $io->confirm(
            'Do you want to configure the integration with a Mercure hub server?',
            false,
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io->reveal(), new PathCollection());

        self::assertEquals($expectedAnswer, $answer);
        $confirm->shouldHaveBeenCalledOnce();
    }
}
