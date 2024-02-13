<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\QrCode;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\QrCode\EnabledForDisabledShortUrlsConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class EnabledForDisabledShortUrlsConfigOptionTest extends TestCase
{
    private EnabledForDisabledShortUrlsConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new EnabledForDisabledShortUrlsConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('QR_CODE_FOR_DISABLED_SHORT_URLS', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('confirm')->with(
            'Should Shlink be able to generate QR codes for short URLs which are not enabled? (Short URLs are not '
            . 'enabled if they have a "valid since" in the future, a "valid until" in the past, or reached the maximum '
            . 'amount of allowed visits)',
        )->willReturn(true);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals(true, $answer);
    }
}
