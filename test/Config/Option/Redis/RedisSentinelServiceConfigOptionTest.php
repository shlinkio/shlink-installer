<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Redis;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Redis\RedisSentinelServiceConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\Redis\RedisServersConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class RedisSentinelServiceConfigOptionTest extends TestCase
{
    private RedisSentinelServiceConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new RedisSentinelServiceConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('REDIS_SENTINEL_SERVICE', $this->configOption->getEnvVar());
    }

    #[Test, DataProvider('provideValidAnswers')]
    public function expectedQuestionIsAsked(?string $answer): void
    {
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('ask')->with(
            'Provide the name of the sentinel service (leave empty if not using redis sentinel)',
        )->willReturn($answer);

        $results = $this->configOption->ask($io, []);

        self::assertEquals($answer, $results);
    }

    public static function provideValidAnswers(): iterable
    {
        yield ['the_answer'];
        yield [null];
    }

    #[Test, DataProvider('provideCurrentOptions')]
    public function shouldBeCalledOnlyIfItDoesNotYetExist(array $currentOptions, bool $expected): void
    {
        self::assertEquals($expected, $this->configOption->shouldBeAsked($currentOptions));
    }

    public static function provideCurrentOptions(): iterable
    {
        yield 'not exists in config' => [[], false];
        yield 'redis enabled in config' => [[RedisServersConfigOption::ENV_VAR => 'bar'], true];
        yield 'exists in config' => [['REDIS_SENTINEL_SERVICE' => 'foo'], false];
    }

    #[Test]
    public function dependsOnRedisServer(): void
    {
        self::assertEquals(RedisServersConfigOption::class, $this->configOption->getDependentOption());
    }
}
