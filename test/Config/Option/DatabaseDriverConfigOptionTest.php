<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\DatabaseDriverConfigOption;
use Shlinkio\Shlink\Installer\Util\PathCollection;
use Symfony\Component\Console\Style\StyleInterface;

class DatabaseDriverConfigOptionTest extends TestCase
{
    private DatabaseDriverConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new DatabaseDriverConfigOption();
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        $this->assertEquals(['entity_manager', 'connection', 'driver'], $this->configOption->getConfigPath());
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
                'SQLite',
            ],
            'MySQL'
        )->willReturn('SQLite');

        $answer = $this->configOption->ask($io->reveal(), new PathCollection());

        $this->assertEquals($expectedAnswer, $answer);
        $choice->shouldHaveBeenCalledOnce();
    }
}
