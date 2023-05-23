<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Tracking;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Tracking\DisableReferrerTrackingConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class DisableReferrerTrackingConfigOptionTest extends TestCase
{
    private DisableReferrerTrackingConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new DisableReferrerTrackingConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('DISABLE_REFERRER_TRACKING', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = true;
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('confirm')->with(
            'Do you want to disable tracking of visitors\' "Referrers"?',
            false,
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }
}
