<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\QrCode;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\QrCode\DefaultFormatConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class DefaultFormatConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private DefaultFormatConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new DefaultFormatConfigOption();
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        self::assertEquals(['qr_codes', 'format'], $this->configOption->getConfigPath());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 'svg';
        $io = $this->prophesize(StyleInterface::class);
        $choice = $io->choice('What\'s the default format for generated QR codes', ['png', 'svg'], 'png')->willReturn(
            $expectedAnswer,
        );

        $answer = $this->configOption->ask($io->reveal(), new PathCollection());

        self::assertEquals($expectedAnswer, $answer);
        $choice->shouldHaveBeenCalledOnce();
    }
}
