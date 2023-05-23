<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Database;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Database\DatabaseDriverConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\Database\DatabaseUnixSocketConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class DatabaseUnixSocketConfigOptionTest extends TestCase
{
    private DatabaseUnixSocketConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new DatabaseUnixSocketConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('DB_UNIX_SOCKET', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = '/var/run/mysqld/mysqld.sock';
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('ask')->with('Unix socket (leave empty to not use a socket)')->willReturn(
            $expectedAnswer,
        );

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }

    #[Test]
    public function dependsOnDriver(): void
    {
        self::assertEquals(DatabaseDriverConfigOption::class, $this->configOption->getDependentOption());
    }
}
