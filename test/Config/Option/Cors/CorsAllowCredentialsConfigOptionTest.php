<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Cors;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Cors\CorsAllowCredentialsConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class CorsAllowCredentialsConfigOptionTest extends TestCase
{
    private CorsAllowCredentialsConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new CorsAllowCredentialsConfigOption();
    }

    #[Test]
    public function expectedEnvVarIsReturned(): void
    {
        self::assertEquals('CORS_ALLOW_CREDENTIALS', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = true;
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('confirm')->with(
            'Do you want browsers to forward credentials to your Shlink server during CORS requests?',
            false,
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }
}
