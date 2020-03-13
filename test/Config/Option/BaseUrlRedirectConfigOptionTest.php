<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\BaseUrlRedirectConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class BaseUrlRedirectConfigOptionTest extends TestCase
{
    private BaseUrlRedirectConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new BaseUrlRedirectConfigOption();
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        $this->assertEquals(['not_found_redirects', 'base_url'], $this->configOption->getConfigPath());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 'the_answer';
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask(
            'Custom URL to redirect to when a user hits Shlink\'s base URL (If no value is provided, the '
            . 'user will see a default "404 not found" page)',
            null,
            Argument::any(),
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io->reveal(), new PathCollection());

        $this->assertEquals($expectedAnswer, $answer);
        $ask->shouldHaveBeenCalledOnce();
    }
}
