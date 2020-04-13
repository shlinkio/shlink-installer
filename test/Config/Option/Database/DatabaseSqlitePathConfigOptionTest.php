<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Database;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\Database\DatabaseDriverConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\Database\DatabaseSqlitePathConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class DatabaseSqlitePathConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private DatabaseSqlitePathConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new DatabaseSqlitePathConfigOption();
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        $this->assertEquals(['entity_manager', 'connection', 'path'], $this->configOption->getConfigPath());
    }

    /** @test */
    public function askReturnsStaticValue(): void
    {
        $io = $this->prophesize(StyleInterface::class)->reveal();
        $this->assertEquals('data/database.sqlite', $this->configOption->ask($io, new PathCollection()));
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
        $buildCollection = static function (string $driver): PathCollection {
            $collection = new PathCollection();
            $collection->setValueInPath($driver, DatabaseDriverConfigOption::CONFIG_PATH);

            return $collection;
        };

        yield 'sqlite' => [$buildCollection(DatabaseDriverConfigOption::SQLITE_DRIVER), true];
        yield 'mysql' => [$buildCollection(DatabaseDriverConfigOption::MYSQL_DRIVER), false];
        yield 'postgres' => [$buildCollection(DatabaseDriverConfigOption::POSTGRES_DRIVER), false];
    }
}
