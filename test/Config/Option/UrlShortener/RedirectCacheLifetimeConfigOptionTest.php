<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\UrlShortener;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
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
    public function returnsExpectedConfig(): void
    {
        self::assertEquals(['url_shortener', 'redirect_cache_lifetime'], $this->configOption->getConfigPath());
    }

    /**
     * @test
     * @dataProvider provideCurrentOptions
     */
    public function shouldBeCalledOnlyIfRedirectStatusIsPermanent(PathCollection $currentOptions, bool $expected): void
    {
        self::assertEquals($expected, $this->configOption->shouldBeAsked($currentOptions));
    }

    public function provideCurrentOptions(): iterable
    {
        $buildCollection = static function (int $status): PathCollection {
            $collection = new PathCollection();
            $collection->setValueInPath($status, RedirectStatusCodeConfigOption::CONFIG_PATH);

            return $collection;
        };

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
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask(
            'How long (in seconds) do you want your redirects to be cached by visitors?',
            '30',
            Argument::any(),
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io->reveal(), new PathCollection());

        self::assertEquals($expectedAnswer, $answer);
        $ask->shouldHaveBeenCalledOnce();
    }
}
