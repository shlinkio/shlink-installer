<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Mercure;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Mercure\MercureAuthCookieNameConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\Mercure\MercureVersionConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class MercureAuthCookieNameConfigOptionTest extends TestCase
{
    private MercureAuthCookieNameConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new MercureAuthCookieNameConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('MERCURE_AUTH_COOKIE_NAME', $this->configOption->getEnvVar());
    }

    #[Test]
    #[TestWith(['legacy', 'mercureAuthorization'])]
    #[TestWith(['stable', '__Secure-mercure_access_token'])]
    public function expectedQuestionIsAsked(string $version, string $expectedDefaultCookieName): void
    {
        $expectedAnswer = 'foobar';
        $io = $this->createMock(StyleInterface::class);
        $io
            ->expects($this->once())
            ->method('ask')
            ->with(
                'Name of the auth cookie for the mercure hub',
                $expectedDefaultCookieName,
            )
            ->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io, [
            MercureVersionConfigOption::ENV_VAR => $version,
        ]);

        self::assertEquals($expectedAnswer, $answer);
    }

    #[Test]
    public function dependsOnMercureEnabled(): void
    {
        self::assertEquals(MercureVersionConfigOption::class, $this->configOption->getDependentOption());
    }
}
