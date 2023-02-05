<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Database;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Database\DatabaseDriverConfigOption;
use Shlinkio\Shlink\Installer\Config\Util\DatabaseDriver;
use Symfony\Component\Console\Style\StyleInterface;

class DatabaseDriverConfigOptionTest extends TestCase
{
    private DatabaseDriverConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new DatabaseDriverConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('DB_DRIVER', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = DatabaseDriver::SQLITE->value;
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('choice')->with(
            'Select database type',
            [
                'MySQL',
                'MariaDB',
                'PostgreSQL',
                'MicrosoftSQL',
                'SQLite',
            ],
            'MySQL',
        )->willReturn('SQLite');

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }
}
