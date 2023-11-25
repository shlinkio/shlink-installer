<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Matomo;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Matomo\MatomoEnabledConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class MatomoEnabledConfigOptionTest extends TestCase
{
    private MatomoEnabledConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new MatomoEnabledConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('MATOMO_ENABLED', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('confirm')->with(
            'Do you want Shlink to send all visits to an external Matomo server?',
            false,
        )->willReturn(true);

        $answer = $this->configOption->ask($io, []);

        self::assertTrue($answer);
    }
}
