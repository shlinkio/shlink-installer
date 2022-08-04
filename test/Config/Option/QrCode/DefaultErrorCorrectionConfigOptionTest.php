<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\QrCode;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Installer\Config\Option\QrCode\DefaultErrorCorrectionConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class DefaultErrorCorrectionConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private DefaultErrorCorrectionConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new DefaultErrorCorrectionConfigOption();
    }

    /** @test */
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('DEFAULT_QR_CODE_ERROR_CORRECTION', $this->configOption->getEnvVar());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 'q';
        $io = $this->prophesize(StyleInterface::class);
        $choice = $io->choice(
            'What\'s the default error correction for generated QR codes',
            [
                'l' => 'Low',
                'm' => 'Medium',
                'q' => 'Quartile',
                'h' => 'High',
            ],
            'l',
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io->reveal(), []);

        self::assertEquals($expectedAnswer, $answer);
        $choice->shouldHaveBeenCalledOnce();
    }
}
