<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Redirect;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\Redirect\InvalidShortUrlRedirectConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class InvalidShortUrlRedirectConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private InvalidShortUrlRedirectConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new InvalidShortUrlRedirectConfigOption();
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        self::assertEquals(['not_found_redirects', 'invalid_short_url'], $this->configOption->getConfigPath());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 'the_answer';
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask(
            'Custom URL to redirect to when a user hits an invalid short URL (If no value is provided, the '
            . 'user will see a default "404 not found" page)',
            null,
            Argument::any(),
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io->reveal(), new PathCollection());

        self::assertEquals($expectedAnswer, $answer);
        $ask->shouldHaveBeenCalledOnce();
    }
}
