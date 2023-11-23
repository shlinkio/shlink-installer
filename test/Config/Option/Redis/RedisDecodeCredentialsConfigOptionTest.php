<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Redis;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Redis\RedisDecodeCredentialsConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\Redis\RedisServersConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class RedisDecodeCredentialsConfigOptionTest extends TestCase
{
    private RedisDecodeCredentialsConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new RedisDecodeCredentialsConfigOption();
    }

    #[Test]
    public function returnsExpectedConfig(): void
    {
        self::assertEquals('REDIS_DECODE_CREDENTIALS', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('confirm')->with(
            'Do you want redis credentials to be URL-decoded? '
            . '(If you provided servers with URL-encoded credentials, this should be "yes")',
            false,
        )->willReturn(true);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals(true, $answer);
    }

    #[Test, DataProvider('provideCurrentOptions')]
    public function shouldBeCalledOnlyIfItDoesNotYetExist(array $currentOptions, bool $expected): void
    {
        self::assertEquals($expected, $this->configOption->shouldBeAsked($currentOptions));
    }

    public static function provideCurrentOptions(): iterable
    {
        yield 'not exists in config' => [[], false];
        yield 'exists in config' => [['REDIS_DECODE_CREDENTIALS' => true], false];
        yield 'redis enabled in config' => [[RedisServersConfigOption::ENV_VAR => 'bar'], true];
    }

    #[Test]
    public function dependsOnRedisServer(): void
    {
        self::assertEquals(RedisServersConfigOption::class, $this->configOption->getDependentOption());
    }
}
