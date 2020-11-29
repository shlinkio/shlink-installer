<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\BasePathConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class BasePathConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private BasePathConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new BasePathConfigOption();
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        self::assertEquals(['router', 'base_path'], $this->configOption->getConfigPath());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 'the_answer';
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask(
            'What is the path from which shlink is going to be served? (Leave empty if you plan to serve '
            . 'shlink from the root of the domain)',
            '',
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io->reveal(), new PathCollection());

        self::assertEquals($expectedAnswer, $answer);
        $ask->shouldHaveBeenCalledOnce();
    }

    /**
     * @test
     * @dataProvider provideCurrentOptions
     */
    public function shouldBeCalledOnlyIfItDoesNotYetExist(PathCollection $currentOptions, bool $expected): void
    {
        self::assertEquals($expected, $this->configOption->shouldBeAsked($currentOptions));
    }

    public function provideCurrentOptions(): iterable
    {
        yield 'not exists in config' => [new PathCollection(), true];
        yield 'exists in config' => [new PathCollection([
            'router' => [
                'base_path' => '/foo',
            ],
        ]), false];
    }
}
