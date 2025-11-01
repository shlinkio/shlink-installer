<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Redis;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Redis\RedisSentinelServiceConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\Redis\RedisServersUserConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class RedisServersUserConfigOptionTest extends TestCase
{
    private RedisServersUserConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new RedisServersUserConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('REDIS_SERVERS_USER', $this->configOption->getEnvVar());
    }

    #[Test, DataProvider('provideValidAnswers')]
    public function expectedQuestionIsAsked(string|null $answer): void
    {
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('ask')->with(
            'Provide a username for your redis connection (leave empty if ACL is not required)',
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
        yield 'sentinel enabled in config' => [[RedisSentinelServiceConfigOption::ENV_VAR => 'bar'], true];
    }

    #[Test]
    public function dependsOnRedisServer(): void
    {
        self::assertEquals(RedisSentinelServiceConfigOption::class, $this->configOption->getDependentOption());
    }
}
