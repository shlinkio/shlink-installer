<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\QrCode;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\QrCode\DefaultFormatConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class DefaultFormatConfigOptionTest extends TestCase
{
    private DefaultFormatConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new DefaultFormatConfigOption();
    }

    /** @test */
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('DEFAULT_QR_CODE_FORMAT', $this->configOption->getEnvVar());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 'svg';
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('choice')->with(
            'What\'s the default format for generated QR codes',
            ['png', 'svg'],
            'png',
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }
}
