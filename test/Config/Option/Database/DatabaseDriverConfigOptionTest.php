<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Database;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\Database\DatabaseDriverConfigOption;
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
    public function returnsExpectedConfig(): void
    {
        self::assertEquals(['entity_manager', 'connection', 'driver'], $this->configOption->getDeprecatedPath());
        self::assertEquals('DB_DRIVER', $this->configOption->getEnvVar());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = DatabaseDriverConfigOption::SQLITE_DRIVER;
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

        $answer = $this->configOption->ask($io->reveal(), new PathCollection());

        self::assertEquals($expectedAnswer, $answer);
        $choice->shouldHaveBeenCalledOnce();
    }
}
