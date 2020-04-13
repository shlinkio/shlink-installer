<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Database;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\Database\DatabaseDriverConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\Database\DatabaseHostConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class DatabaseHostConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private DatabaseHostConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new DatabaseHostConfigOption();
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        $this->assertEquals(['entity_manager', 'connection', 'host'], $this->configOption->getConfigPath());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 'the_answer';
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask('Database host', 'localhost')->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io->reveal(), new PathCollection());

        $this->assertEquals($expectedAnswer, $answer);
        $ask->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function dependsOnDriver(): void
    {
        $this->assertEquals(DatabaseDriverConfigOption::class, $this->configOption->getDependentOption());
    }

    /**
     * @test
     * @dataProvider provideCurrentOptions
     */
    public function shouldBeCalledOnlyIfNotSetAndDriverIsNotSqlite(PathCollection $currentOptions, bool $expected): void
    {
        $this->assertEquals($expected, $this->configOption->shouldBeAsked($currentOptions));
    }

    public function provideCurrentOptions(): iterable
    {
        $buildCollection = static function (string $driver, bool $withHost = false): PathCollection {
            $collection = new PathCollection();
            $collection->setValueInPath($driver, DatabaseDriverConfigOption::CONFIG_PATH);
            if ($withHost) {
                $collection->setValueInPath('the_host', ['entity_manager', 'connection', 'host']);
            }

            return $collection;
        };

        yield 'sqlite' => [$buildCollection(DatabaseDriverConfigOption::SQLITE_DRIVER), false];
        yield 'mysql' => [$buildCollection(DatabaseDriverConfigOption::MYSQL_DRIVER), true];
        yield 'postgres' => [$buildCollection(DatabaseDriverConfigOption::POSTGRES_DRIVER), true];
        yield 'mysql with value' => [$buildCollection(DatabaseDriverConfigOption::MYSQL_DRIVER, true), false];
        yield 'postgres with value' => [$buildCollection(DatabaseDriverConfigOption::POSTGRES_DRIVER, true), false];
    }
}
