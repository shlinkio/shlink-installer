<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Matomo;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Matomo\MatomoSiteIdConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class MatomoSiteIdConfigOptionTest extends TestCase
{
    private MatomoSiteIdConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new MatomoSiteIdConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('MATOMO_SITE_ID', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = '12345';
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('ask')->with('Matomo site ID')->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }
}
