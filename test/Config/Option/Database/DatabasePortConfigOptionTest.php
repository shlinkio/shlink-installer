<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Database;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\Database\DatabaseDriverConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\Database\DatabasePortConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class DatabasePortConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private DatabasePortConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new DatabasePortConfigOption();
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        $this->assertEquals(['entity_manager', 'connection', 'port'], $this->configOption->getConfigPath());
    }

    /**
     * @test
     * @dataProvider provideCurrentOptions
     */
    public function expectedQuestionIsAsked(PathCollection $currentOptions, string $expectedPort): void
    {
        $expectedAnswer = 'the_answer';
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask('Database port', $expectedPort)->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io->reveal(), $currentOptions);

        $this->assertEquals($expectedAnswer, $answer);
        $ask->shouldHaveBeenCalledOnce();
    }

    public function provideCurrentOptions(): iterable
    {
        $buildCollection = static function (string $driver): PathCollection {
            $collection = new PathCollection();
            $collection->setValueInPath($driver, DatabaseDriverConfigOption::CONFIG_PATH);

            return $collection;
        };

        yield 'mysql' => [$buildCollection(DatabaseDriverConfigOption::MYSQL_DRIVER), '3306'];
        yield 'postgres' => [$buildCollection(DatabaseDriverConfigOption::POSTGRES_DRIVER), '5432'];
        yield 'mssql' => [$buildCollection(DatabaseDriverConfigOption::MSSQL_DRIVER), '1433'];
        yield 'sqlite' => [$buildCollection(DatabaseDriverConfigOption::SQLITE_DRIVER), ''];
        yield 'unsupported' => [$buildCollection('unsupported'), ''];
    }
}
