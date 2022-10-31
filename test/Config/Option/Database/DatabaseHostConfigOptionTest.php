<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Database;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Database\DatabaseDriverConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\Database\DatabaseHostConfigOption;
use Shlinkio\Shlink\Installer\Config\Util\DatabaseDriver;
use Symfony\Component\Console\Style\StyleInterface;

class DatabaseHostConfigOptionTest extends TestCase
{
    private DatabaseHostConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new DatabaseHostConfigOption();
    }

    /** @test */
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('DB_HOST', $this->configOption->getEnvVar());
    }

    /**
     * @test
     * @dataProvider provideDrivers
     */
    public function expectedQuestionIsAsked(string $driver, string $expectedQuestionText): void
    {
        $expectedAnswer = 'the_answer';
        $collection = [DatabaseDriverConfigOption::ENV_VAR => $driver];

        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('ask')->with($expectedQuestionText, 'localhost')->willReturn(
            $expectedAnswer,
        );

        $answer = $this->configOption->ask($io, $collection);

        self::assertEquals($expectedAnswer, $answer);
    }

    public function provideDrivers(): iterable
    {
        yield 'mysql' => [DatabaseDriver::MYSQL->value, 'Database host'];
        yield 'mssql' => [DatabaseDriver::MSSQL->value, 'Database host'];
        yield 'postgres' => [DatabaseDriver::POSTGRES->value, 'Database host (or unix socket)'];
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
    public function shouldBeCalledOnlyIfNotSetAndDriverIsNotSqlite(array $currentOptions, bool $expected): void
    {
        self::assertEquals($expected, $this->configOption->shouldBeAsked($currentOptions));
    }

    public function provideCurrentOptions(): iterable
    {
        $buildCollection = static function (string $driver, bool $withHost = false): array {
            $collection = [DatabaseDriverConfigOption::ENV_VAR => $driver];
            if ($withHost) {
                $collection['DB_HOST'] = 'the_host';
            }

            return $collection;
        };

        yield 'sqlite' => [$buildCollection(DatabaseDriver::SQLITE->value), false];
        yield 'mysql' => [$buildCollection(DatabaseDriver::MYSQL->value), true];
        yield 'postgres' => [$buildCollection(DatabaseDriver::POSTGRES->value), true];
        yield 'mysql with value' => [$buildCollection(DatabaseDriver::MYSQL->value, true), false];
        yield 'postgres with value' => [$buildCollection(DatabaseDriver::POSTGRES->value, true), false];
    }
}
