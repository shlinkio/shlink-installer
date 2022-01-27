<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Database;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\Database\DatabaseNameConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class DatabaseNameConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private DatabaseNameConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new DatabaseNameConfigOption();
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        self::assertEquals(['entity_manager', 'connection', 'dbname'], $this->configOption->getDeprecatedPath());
        self::assertEquals('DB_NAME', $this->configOption->getEnvVar());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 'the_answer';
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask('Database name', 'shlink')->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io->reveal(), new PathCollection());

        self::assertEquals($expectedAnswer, $answer);
        $ask->shouldHaveBeenCalledOnce();
    }
}
