<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Redirect;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Installer\Config\Option\Redirect\Regular404RedirectConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class Regular404RedirectConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private Regular404RedirectConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new Regular404RedirectConfigOption();
    }

    /** @test */
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('DEFAULT_REGULAR_404_REDIRECT', $this->configOption->getEnvVar());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 'the_answer';
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask(
            'Custom URL to redirect to when a user hits a not found URL other than an invalid short URL '
            . '(If no value is provided, the user will see a default "404 not found" page)',
            null,
            Argument::any(),
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io->reveal(), []);

        self::assertEquals($expectedAnswer, $answer);
        $ask->shouldHaveBeenCalledOnce();
    }
}
