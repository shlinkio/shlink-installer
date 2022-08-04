<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\UrlShortener;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Installer\Config\Option\UrlShortener\RedirectStatusCodeConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class RedirectStatusCodeConfigOptionTest extends TestCase
{
    use ProphecyTrait;

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
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->choice(
            'What kind of redirect do you want your short URLs to have?',
            Argument::type('array'),
            Argument::any(),
        )->willReturn($choice);

        $answer = $this->configOption->ask($io->reveal(), []);

        self::assertEquals($expectedAnswer, $answer);
        $ask->shouldHaveBeenCalledOnce();
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
