<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\RedisServersConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class RedisServersConfigOptionTest extends TestCase
{
    private RedisServersConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new RedisServersConfigOption();
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        $this->assertEquals(['cache', 'redis'], $this->configOption->getConfigPath());
    }

    /**
     * @test
     * @dataProvider provideAnswers
     */
    public function expectedQuestionIsAsked(?string $answered, ?array $expectedAnswer): void
    {
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask(
            'Provide a comma-separated list of redis server URIs which will be used for shared caching purposes under '
            . 'shlink multi-instance contexts (Leave empty if you don\'t want to use redis cache)',
        )->willReturn($answered);

        $answer = $this->configOption->ask($io->reveal(), new PathCollection());

        $this->assertEquals($expectedAnswer, $answer);
        $ask->shouldHaveBeenCalledOnce();
    }

    public function provideAnswers(): iterable
    {
        yield 'empty answer' => [null, null];
        yield 'one server' => ['foo', ['servers' => ['foo']]];
        yield 'multiple servers' => ['foo,bar,baz', ['servers' => ['foo', 'bar', 'baz']]];
    }
}
