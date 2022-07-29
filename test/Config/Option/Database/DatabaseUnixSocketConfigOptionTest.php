<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Database;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Installer\Config\Option\Database\DatabaseDriverConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\Database\DatabaseUnixSocketConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class DatabaseUnixSocketConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private DatabaseUnixSocketConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new DatabaseUnixSocketConfigOption();
    }

    /** @test */
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('DB_UNIX_SOCKET', $this->configOption->getEnvVar());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = '/var/run/mysqld/mysqld.sock';
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask('Unix socket (leave empty to not use a socket)')->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io->reveal(), []);

        self::assertEquals($expectedAnswer, $answer);
        $ask->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function dependsOnDriver(): void
    {
        self::assertEquals(DatabaseDriverConfigOption::class, $this->configOption->getDependentOption());
    }
}
