<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\QrCode;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\QrCode\DefaultMarginConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class DefaultMarginConfigOptionTest extends TestCase
{
    private DefaultMarginConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new DefaultMarginConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('DEFAULT_QR_CODE_MARGIN', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 10;
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('ask')->with(
            'What\'s the default margin, in pixels, you want generated QR codes to have',
            '0',
            $this->anything(),
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }
}
