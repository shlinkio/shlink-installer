<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Mercure;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Mercure\MercureVersionConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class MercureVersionConfigOptionTest extends TestCase
{
    private MercureVersionConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new MercureVersionConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('MERCURE_VERSION', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 'v1';
        $io = $this->createMock(StyleInterface::class);
        $io
            ->expects($this->once())
            ->method('choice')
            ->with(
                'Version of the mercure hub',
                ['v0', 'v1'],
                'v0',
            )
            ->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }
}
