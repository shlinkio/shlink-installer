<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\UrlShortener;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Installer\Config\Option\UrlShortener\RedirectCacheLifeTimeConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\UrlShortener\RedirectStatusCodeConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class RedirectCacheLifetimeConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private RedirectCacheLifeTimeConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new RedirectCacheLifeTimeConfigOption();
    }

    /** @test */
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('REDIRECT_CACHE_LIFETIME', $this->configOption->getEnvVar());
    }

    /**
     * @test
     * @dataProvider provideCurrentOptions
     */
    public function shouldBeCalledOnlyIfRedirectStatusIsPermanent(array $currentOptions, bool $expected): void
    {
        self::assertEquals($expected, $this->configOption->shouldBeAsked($currentOptions));
    }

    public function provideCurrentOptions(): iterable
    {
        $buildCollection = static fn (int $status): array => [RedirectStatusCodeConfigOption::ENV_VAR => $status];

        yield 'status 301' => [$buildCollection(301), true];
        yield 'status 302' => [$buildCollection(302), false];
    }

    /** @test */
    public function dependsOnStatusCode(): void
    {
        self::assertEquals(RedirectStatusCodeConfigOption::class, $this->configOption->getDependentOption());
    }

    /** @test */
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
