<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\UrlShortener;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\UrlShortener\RedirectStatusCodeConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class RedirectStatusCodeConfigOptionTest extends TestCase
{
    private RedirectStatusCodeConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new RedirectStatusCodeConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('REDIRECT_STATUS_CODE', $this->configOption->getEnvVar());
    }

    #[Test, DataProvider('provideChoices')]
    public function expectedQuestionIsAsked(string $choice, int $expectedAnswer): void
    {
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('choice')->with(
            'What kind of redirect do you want your short URLs to have?',
            $this->isArray(),
            $this->anything(),
        )->willReturn($choice);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }

    public static function provideChoices(): iterable
    {
        yield '302 redirect' => [
            'All visits will always be tracked. Not that good for SEO. Only GET requests will be redirected.',
            302,
        ];
        yield '301 redirect' => [
            'Best option for SEO. Redirect will be cached for a short period of time, making some visits not to be '
            . 'tracked. Only GET requests will be redirected.',
            301,
        ];
        yield '307 redirect' => ['Same as 302, but Shlink will also redirect on non-GET requests.', 307];
        yield '308 redirect' => ['Same as 301, but Shlink will also redirect on non-GET requests.', 308];
    }
}
