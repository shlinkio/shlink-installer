<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\DatabaseDriverConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\DatabaseMySqlOptionsConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class DatabaseMySqlOptionsConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private DatabaseMySqlOptionsConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new DatabaseMySqlOptionsConfigOption();
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        $this->assertEquals(['entity_manager', 'connection', 'driverOptions'], $this->configOption->getConfigPath());
    }

    /** @test */
    public function askReturnsStaticValue(): void
    {
        $io = $this->prophesize(StyleInterface::class)->reveal();
        $this->assertEquals([1002 => 'SET NAMES utf8'], $this->configOption->ask($io, new PathCollection()));
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

        yield 'sqlite' => [$buildCollection(DatabaseDriverConfigOption::SQLITE_DRIVER), false];
        yield 'mysql' => [$buildCollection(DatabaseDriverConfigOption::MYSQL_DRIVER), true];
        yield 'postgres' => [$buildCollection(DatabaseDriverConfigOption::POSTGRES_DRIVER), false];
    }
}
