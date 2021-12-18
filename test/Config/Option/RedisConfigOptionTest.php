<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\RedisConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class RedisConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private RedisConfigOption $configOption;
    private ObjectProphecy $io;

    public function setUp(): void
    {
        $this->configOption = new RedisConfigOption();
        $this->io = $this->prophesize(StyleInterface::class);
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        self::assertEquals(['cache', 'redis'], $this->configOption->getConfigPath());
    }

    /** @test */
    public function onlyOneQuestionIsAskedWhenNoRedisConfigIsRequested(): void
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
    public function sentinelsAreNotRequestedIfSentinelServiceIsNotProvided(
        ?string $serversAnswer,
        array $expectedServers,
    ): void {
        $confirm = $this->io->confirm(
            'Do you want to use a redis instance, redis cluster or redis sentinels as a shared cache for Shlink? '
            . '(recommended if you run a cluster of Shlink instances)',
            false,
        )->willReturn(true);
        $askSentinelService = $this->io->ask(
            'Provide the name of the sentinel service (leave empty if not using redis sentinel)',
        )->willReturn(null);
        $askSentinels = $this->io->ask('Provide a comma-separated list of sentinel instance URIs');
        $askServers = $this->io->ask(
            'Provide a comma-separated list of redis server URIs. If more than one is provided, it will be considered '
            . 'a redis cluster',
        )->willReturn($serversAnswer);

        $result = $this->configOption->ask($this->io->reveal(), new PathCollection());

        self::assertEquals([
            'servers' => $expectedServers,
            'sentinel_service' => null,
        ], $result);
        $confirm->shouldHaveBeenCalledOnce();
        $askSentinelService->shouldHaveBeenCalledOnce();
        $askSentinels->shouldNotHaveBeenCalled();
        $askServers->shouldHaveBeenCalledOnce();
    }

    /**
     * @test
     * @dataProvider provideAnswers
     */
    public function sentinelsAreRequestedIfSentinelServiceIsProvided(
        ?string $serversAnswer,
        array $expectedServers,
    ): void {
        $confirm = $this->io->confirm(
            'Do you want to use a redis instance, redis cluster or redis sentinels as a shared cache for Shlink? '
            . '(recommended if you run a cluster of Shlink instances)',
            false,
        )->willReturn(true);
        $askSentinelService = $this->io->ask(
            'Provide the name of the sentinel service (leave empty if not using redis sentinel)',
        )->willReturn('my_service');
        $askSentinels = $this->io->ask('Provide a comma-separated list of sentinel instance URIs')->willReturn(
            $serversAnswer,
        );
        $askServers = $this->io->ask(
            'Provide a comma-separated list of redis server URIs. If more than one is provided, it will be considered '
            . 'a redis cluster',
        );

        $result = $this->configOption->ask($this->io->reveal(), new PathCollection());

        self::assertEquals([
            'servers' => $expectedServers,
            'sentinel_service' => 'my_service',
        ], $result);
        $confirm->shouldHaveBeenCalledOnce();
        $askSentinelService->shouldHaveBeenCalledOnce();
        $askSentinels->shouldHaveBeenCalledOnce();
        $askServers->shouldNotHaveBeenCalled();
    }

    public function provideAnswers(): iterable
    {
        yield 'one server' => ['foo', ['foo']];
        yield 'one server with spaces' => [' foo  ', ['foo']];
        yield 'multiple servers' => ['foo,bar,baz', ['foo', 'bar', 'baz']];
        yield 'multiple servers with spaces' => ['foo  ,bar   , baz  ', ['foo', 'bar', 'baz']];
    }
}
