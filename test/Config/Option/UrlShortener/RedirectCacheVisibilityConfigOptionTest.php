<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\UrlShortener;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\UrlShortener\RedirectCacheVisibilityConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class RedirectCacheVisibilityConfigOptionTest extends TestCase
{
    private RedirectCacheVisibilityConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new RedirectCacheVisibilityConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('REDIRECT_CACHE_VISIBILITY', $this->configOption->getEnvVar());
    }

    #[Test]
    #[TestWith([true, 'public'])]
    #[TestWith([false, 'private'])]
    public function expectedQuestionIsAsked(bool $confirmAnswer, string $expectedResult): void
    {
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('confirm')->with(
            'Do you want redirects to be cached by reverse proxies?',
            false,
        )->willReturn($confirmAnswer);

        $result = $this->configOption->ask($io, []);

        self::assertEquals($expectedResult, $result);
    }
}
