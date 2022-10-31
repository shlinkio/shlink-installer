<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Mercure;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Mercure\MercurePublicUrlConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class MercurePublicUrlConfigOptionTest extends TestCase
{
    private MercurePublicUrlConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new MercurePublicUrlConfigOption(fn () => false);
    }

    /** @test */
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('MERCURE_PUBLIC_HUB_URL', $this->configOption->getEnvVar());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 'foobar.com';
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('ask')->with(
            'Public URL of the mercure hub server',
            $this->anything(),
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }
}
