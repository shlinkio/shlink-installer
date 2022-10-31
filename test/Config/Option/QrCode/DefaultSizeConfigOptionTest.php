<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\QrCode;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Installer\Config\Option\QrCode\DefaultSizeConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class DefaultSizeConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private DefaultSizeConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new DefaultSizeConfigOption();
    }

    /** @test */
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('DEFAULT_QR_CODE_SIZE', $this->configOption->getEnvVar());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 500;
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('ask')->with(
            'What\'s the default size, in pixels, you want generated QR codes to have (50 to 1000)',
            '300',
            $this->anything(),
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }
}
