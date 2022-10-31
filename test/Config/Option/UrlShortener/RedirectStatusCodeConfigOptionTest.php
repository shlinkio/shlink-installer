<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\UrlShortener;

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

    /** @test */
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('REDIRECT_STATUS_CODE', $this->configOption->getEnvVar());
    }

    /**
     * @test
     * @dataProvider provideChoices
     */
    public function expectedQuestionIsAsked(string $choice, int $expectedAnswer): void
    {
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('choice')->with(
            'What kind of redirect do you want your short URLs to have?',
            $this->isType('array'),
            $this->anything(),
        )->willReturn($choice);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }

    public function provideChoices(): iterable
    {
        yield '302 redirect' => ['All visits will always be tracked. Not that good for SEO.', 302];
        yield '301 redirect' => [
            'Best option for SEO. Redirect will be cached for a short period of time, making some visits not to be '
            . 'tracked.',
            301,
        ];
    }
}
