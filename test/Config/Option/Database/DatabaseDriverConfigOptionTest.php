<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Database;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Installer\Config\Option\Database\DatabaseDriverConfigOption;
use Shlinkio\Shlink\Installer\Config\Util\DatabaseDriver;
use Symfony\Component\Console\Style\StyleInterface;

class DatabaseDriverConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private DatabaseDriverConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new DatabaseDriverConfigOption();
    }

    /** @test */
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('DB_DRIVER', $this->configOption->getEnvVar());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = DatabaseDriver::SQLITE->value;
        $io = $this->prophesize(StyleInterface::class);
        $choice = $io->choice(
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

        $answer = $this->configOption->ask($io->reveal(), []);

        self::assertEquals($expectedAnswer, $answer);
        $choice->shouldHaveBeenCalledOnce();
    }
}
