<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Redis;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\Redis\RedisSentinelServiceConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\Redis\RedisServersConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class RedisSentinelServiceConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private RedisSentinelServiceConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new RedisSentinelServiceConfigOption();
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        self::assertEquals(['cache', 'redis', 'sentinel_service'], $this->configOption->getDeprecatedPath());
        self::assertEquals('REDIS_SENTINEL_SERVICE', $this->configOption->getEnvVar());
    }

    /**
     * @test
     * @dataProvider provideValidAnswers
     */
    public function expectedQuestionIsAsked(?string $answer): void
    {
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask(
            'Provide the name of the sentinel service (leave empty if not using redis sentinel)',
        )->willReturn($answer);

        $results = $this->configOption->ask($io->reveal(), new PathCollection());

        self::assertEquals($answer, $results);
        $ask->shouldHaveBeenCalledOnce();
    }

    public function provideValidAnswers(): iterable
    {
        yield ['the_answer'];
        yield [null];
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
            'REDIS_SENTINEL_SERVICE' => 'foo',
        ]), false];
    }

    /** @test */
    public function dependsOnRedisServer(): void
    {
        self::assertEquals(RedisServersConfigOption::class, $this->configOption->getDependentOption());
    }
}
