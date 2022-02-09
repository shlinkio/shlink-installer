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
        self::assertEquals(['entity_manager', 'connection', 'host'], $this->configOption->getDeprecatedPath());
        self::assertEquals('DB_HOST', $this->configOption->getEnvVar());
    }

    /**
     * @test
     * @dataProvider provideDrivers
     */
    public function expectedQuestionIsAsked(string $driver, string $expectedQuestionText): void
    {
        $expectedAnswer = 'the_answer';
        $collection = new PathCollection();
        $collection->setValueInPath($driver, DatabaseDriverConfigOption::CONFIG_PATH);

        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask($expectedQuestionText, 'localhost')->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io->reveal(), $collection);

        self::assertEquals($expectedAnswer, $answer);
        $ask->shouldHaveBeenCalledOnce();
    }

    public function provideDrivers(): iterable
    {
        yield 'mysql' => [DatabaseDriverConfigOption::MYSQL_DRIVER, 'Database host'];
        yield 'mssql' => [DatabaseDriverConfigOption::MSSQL_DRIVER, 'Database host'];
        yield 'postgres' => [
            DatabaseDriverConfigOption::POSTGRES_DRIVER,
            'Database host (or unix socket)',
        ];
    }

    /** @test */
    public function dependsOnDriver(): void
    {
        self::assertEquals(DatabaseDriverConfigOption::class, $this->configOption->getDependentOption());
    }

    /**
     * @test
     * @dataProvider provideCurrentOptions
     */
    public function shouldBeCalledOnlyIfNotSetAndDriverIsNotSqlite(PathCollection $currentOptions, bool $expected): void
    {
        self::assertEquals($expected, $this->configOption->shouldBeAsked($currentOptions));
    }

    public function provideCurrentOptions(): iterable
    {
        $buildCollection = static function (string $driver, bool $withHost = false): PathCollection {
            $collection = new PathCollection();
            $collection->setValueInPath($driver, DatabaseDriverConfigOption::CONFIG_PATH);
            if ($withHost) {
                $collection->setValueInPath('the_host', ['DB_HOST']);
            }

            return $collection;
        };

        yield 'sqlite' => [$buildCollection(DatabaseDriverConfigOption::SQLITE_DRIVER), false];
        yield 'old sqlite' => [$buildCollection('pdo_sqlite'), false];
        yield 'mysql' => [$buildCollection(DatabaseDriverConfigOption::MYSQL_DRIVER), true];
        yield 'postgres' => [$buildCollection(DatabaseDriverConfigOption::POSTGRES_DRIVER), true];
        yield 'mysql with value' => [$buildCollection(DatabaseDriverConfigOption::MYSQL_DRIVER, true), false];
        yield 'postgres with value' => [$buildCollection(DatabaseDriverConfigOption::POSTGRES_DRIVER, true), false];
    }
}
