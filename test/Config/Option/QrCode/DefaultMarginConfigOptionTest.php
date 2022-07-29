<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\QrCode;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Installer\Config\Option\QrCode\DefaultMarginConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class DefaultMarginConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private DefaultMarginConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new DefaultMarginConfigOption();
    }

    /** @test */
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('DEFAULT_QR_CODE_MARGIN', $this->configOption->getEnvVar());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 10;
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask(
            'What\'s the default margin, in pixels, you want generated QR codes to have',
            '0',
            Argument::any(),
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io->reveal(), []);

        self::assertEquals($expectedAnswer, $answer);
        $ask->shouldHaveBeenCalledOnce();
    }
}
