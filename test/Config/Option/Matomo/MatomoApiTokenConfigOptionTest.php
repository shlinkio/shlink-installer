<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Matomo;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Matomo\MatomoApiTokenConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class MatomoApiTokenConfigOptionTest extends TestCase
{
    private MatomoApiTokenConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new MatomoApiTokenConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('MATOMO_API_TOKEN', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 'abc123';
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('ask')->with('Matomo API token')->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }
}
