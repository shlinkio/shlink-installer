<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Mercure;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Mercure\MercureJwtSecretConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class MercureJwtSecretConfigOptionTest extends TestCase
{
    private MercureJwtSecretConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new MercureJwtSecretConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('MERCURE_JWT_SECRET', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 'foobar.com';
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('ask')->with(
            'The secret key known by the mercure hub server to validate JWTs',
            $this->anything(),
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }
}
