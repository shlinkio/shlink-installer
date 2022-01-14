<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Redis;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\Redis\RedisServersConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class RedisServersConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private RedisServersConfigOption $configOption;
    private ObjectProphecy $io;

    public function setUp(): void
    {
        $this->configOption = new RedisServersConfigOption();
        $this->io = $this->prophesize(StyleInterface::class);
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        self::assertEquals(['cache', 'redis', 'servers'], $this->configOption->getDeprecatedPath());
        self::assertEquals('REDIS_SERVERS', $this->configOption->getEnvVar());
    }

    /** @test */
    public function serversAreNotRequestedWhenNoRedisConfigIsProvided(): void
    {
        $confirm = $this->io->confirm(
            'Do you want to use a redis instance, redis cluster or redis sentinels as a shared cache for Shlink? '
            . '(recommended if you run a cluster of Shlink instances)',
            false,
        )->willReturn(false);
        $ask = $this->io->ask(Argument::cetera());

        $answer = $this->configOption->ask($this->io->reveal(), new PathCollection());

        self::assertNull($answer);
        $confirm->shouldHaveBeenCalledOnce();
        $ask->shouldNotHaveBeenCalled();
    }

    /**
     * @test
     * @dataProvider provideAnswers
     */
    public function serversAreRequestedWhenRedisConfigIsProvided(
        ?string $serversAnswer,
        array $expectedServers,
    ): void {
        $confirm = $this->io->confirm(
            'Do you want to use a redis instance, redis cluster or redis sentinels as a shared cache for Shlink? '
            . '(recommended if you run a cluster of Shlink instances)',
            false,
        )->willReturn(true);
        $askServers = $this->io->ask(
            'Provide a comma-separated list of URIs (redis servers/sentinel instances)',
        )->willReturn($serversAnswer);

        $result = $this->configOption->ask($this->io->reveal(), new PathCollection());

        self::assertEquals($expectedServers, $result);
        $confirm->shouldHaveBeenCalledOnce();
        $askServers->shouldHaveBeenCalledOnce();
    }

    public function provideAnswers(): iterable
    {
        yield 'one server' => ['foo', ['foo']];
        yield 'one server with spaces' => [' foo  ', ['foo']];
        yield 'multiple servers' => ['foo,bar,baz', ['foo', 'bar', 'baz']];
        yield 'multiple servers with spaces' => ['foo  ,bar   , baz  ', ['foo', 'bar', 'baz']];
    }
}
