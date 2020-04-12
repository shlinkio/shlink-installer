<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\ValidateUrlConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class ValidateUrlConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private ValidateUrlConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new ValidateUrlConfigOption();
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        $this->assertEquals(['url_shortener', 'validate_url'], $this->configOption->getConfigPath());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = true;
        $io = $this->prophesize(StyleInterface::class);
        $confirm = $io->confirm('Do you want to validate long urls by 200 HTTP status code on response?')->willReturn(
            $expectedAnswer,
        );

        $answer = $this->configOption->ask($io->reveal(), new PathCollection());

        $this->assertEquals($expectedAnswer, $answer);
        $confirm->shouldHaveBeenCalledOnce();
    }
}
