<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Cors;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Cors\CorsMaxAgeConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class CorsMaxAgeConfigOptionTest extends TestCase
{
    private CorsMaxAgeConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new CorsMaxAgeConfigOption();
    }

    #[Test]
    public function expectedEnvVarIsReturned(): void
    {
        self::assertEquals('CORS_MAX_AGE', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 60;
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('ask')->with(
            'How long (in seconds) do you want CORS config to be cached by browsers?',
            '3600',
            $this->anything(),
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }
}
