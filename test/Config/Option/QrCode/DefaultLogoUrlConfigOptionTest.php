<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\QrCode;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\QrCode\DefaultLogoUrlConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class DefaultLogoUrlConfigOptionTest extends TestCase
{
    private DefaultLogoUrlConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new DefaultLogoUrlConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('DEFAULT_QR_CODE_LOGO_URL', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 'the_answer';
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('ask')->with(
            'Provide a URL for a logo to be placed inside the QR code (leave empty to use no logo)',
            null,
            $this->anything(),
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }
}
