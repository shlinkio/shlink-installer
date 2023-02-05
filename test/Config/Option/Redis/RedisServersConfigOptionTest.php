<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Redis;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Redis\RedisServersConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class RedisServersConfigOptionTest extends TestCase
{
    private RedisServersConfigOption $configOption;
    private MockObject & StyleInterface $io;

    public function setUp(): void
    {
        $this->configOption = new RedisServersConfigOption();
        $this->io = $this->createMock(StyleInterface::class);
    }

    /** @test */
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('REDIS_SERVERS', $this->configOption->getEnvVar());
    }

    /** @test */
    public function serversAreNotRequestedWhenNoRedisConfigIsProvided(): void
    {
        $this->io->expects($this->once())->method('confirm')->with(
            'Do you want to use a redis instance, redis cluster or redis sentinels as a shared cache for Shlink? '
            . '(recommended if you run a cluster of Shlink instances)',
            false,
        )->willReturn(false);
        $this->io->expects($this->never())->method('ask');

        $answer = $this->configOption->ask($this->io, []);

        self::assertNull($answer);
    }

    /**
     * @test
     * @dataProvider provideAnswers
     */
    public function serversAreRequestedWhenRedisConfigIsProvided(?string $serversAnswer): void
    {
        $this->io->expects($this->once())->method('confirm')->with(
            'Do you want to use a redis instance, redis cluster or redis sentinels as a shared cache for Shlink? '
            . '(recommended if you run a cluster of Shlink instances)',
            false,
        )->willReturn(true);
        $this->io->expects($this->once())->method('ask')->with(
            'Provide a comma-separated list of URIs (redis servers/sentinel instances)',
        )->willReturn($serversAnswer);

        $result = $this->configOption->ask($this->io, []);

        self::assertEquals($serversAnswer, $result);
    }

    public static function provideAnswers(): iterable
    {
        yield 'one server' => ['foo'];
        yield 'multiple servers' => ['foo,bar,baz'];
    }
}
