<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\UrlShortener;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Installer\Config\Option\UrlShortener\AutoResolveTitlesConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class AutoResolveTitlesConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private AutoResolveTitlesConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new AutoResolveTitlesConfigOption();
    }

    /** @test */
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('AUTO_RESOLVE_TITLES', $this->configOption->getEnvVar());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = true;
        $io = $this->prophesize(StyleInterface::class);
        $confirm = $io->confirm(
            'Do you want Shlink to resolve the short URL title based on the long URL \'s title tag (if any)? '
                . 'Otherwise, it will be kept empty unless explicitly provided.',
            false,
        )->willReturn(
            $expectedAnswer,
        );

        $answer = $this->configOption->ask($io->reveal(), []);

        self::assertEquals($expectedAnswer, $answer);
        $confirm->shouldHaveBeenCalledOnce();
    }
}
