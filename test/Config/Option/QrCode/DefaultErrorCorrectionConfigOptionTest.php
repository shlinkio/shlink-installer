<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\QrCode;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\QrCode\DefaultErrorCorrectionConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class DefaultErrorCorrectionConfigOptionTest extends TestCase
{
    private DefaultErrorCorrectionConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new DefaultErrorCorrectionConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('DEFAULT_QR_CODE_ERROR_CORRECTION', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 'q';
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('choice')->with(
            'What\'s the default error correction for generated QR codes',
            [
                'l' => 'Low',
                'm' => 'Medium',
                'q' => 'Quartile',
                'h' => 'High',
            ],
            'l',
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }
}
