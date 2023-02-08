<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Mercure;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Mercure\EnableMercureConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class EnableMercureConfigOptionTest extends TestCase
{
    private EnableMercureConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new EnableMercureConfigOption(fn () => false);
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('MERCURE_ENABLED', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = true;
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('confirm')->with(
            'Do you want Shlink to publish real-time updates in a Mercure hub server?',
            false,
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }
}
