<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Redis;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\Redis\RedisPubSubConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\Redis\RedisServersConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class RedisPubSubConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private RedisPubSubConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new RedisPubSubConfigOption();
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        self::assertEquals('REDIS_PUB_SUB_ENABLED', $this->configOption->getEnvVar());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $io = $this->prophesize(StyleInterface::class);
        $confirm = $io->confirm(
            'Do you want Shlink to publish real-time updates in this Redis instance/cluster?',
            false,
        )->willReturn(true);

        $answer = $this->configOption->ask($io->reveal(), new PathCollection());

        self::assertEquals(true, $answer);
        $confirm->shouldHaveBeenCalledOnce();
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
        yield 'not exists in config' => [new PathCollection(), false];
        yield 'redis enabled in config' => [new PathCollection([
            RedisServersConfigOption::ENV_VAR => 'bar',
        ]), true];
        yield 'exists in config' => [new PathCollection([
            'REDIS_PUB_SUB_ENABLED' => true,
        ]), false];
    }

    /** @test */
    public function dependsOnRedisServer(): void
    {
        self::assertEquals(RedisServersConfigOption::class, $this->configOption->getDependentOption());
    }
}
