<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\UrlShortener;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\UrlShortener\RedirectCacheLifeTimeConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\UrlShortener\RedirectStatusCodeConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class RedirectCacheLifetimeConfigOptionTest extends TestCase
{
    private RedirectCacheLifeTimeConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new RedirectCacheLifeTimeConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('REDIRECT_CACHE_LIFETIME', $this->configOption->getEnvVar());
    }

    #[Test, DataProvider('provideCurrentOptions')]
    public function shouldBeCalledOnlyIfRedirectStatusIsPermanent(array $currentOptions, bool $expected): void
    {
        self::assertEquals($expected, $this->configOption->shouldBeAsked($currentOptions));
    }

    public static function provideCurrentOptions(): iterable
    {
        $buildCollection = static fn (int $status): array => [RedirectStatusCodeConfigOption::ENV_VAR => $status];

        yield 'status 301' => [$buildCollection(301), true];
        yield 'status 302' => [$buildCollection(302), false];
        yield 'status 307' => [$buildCollection(307), false];
        yield 'status 308' => [$buildCollection(308), true];
    }

    #[Test]
    public function dependsOnStatusCode(): void
    {
        self::assertEquals(RedirectStatusCodeConfigOption::class, $this->configOption->getDependentOption());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 60;
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('ask')->with(
            'How long (in seconds) do you want your redirects to be cached by visitors?',
            '30',
            $this->anything(),
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }
}
