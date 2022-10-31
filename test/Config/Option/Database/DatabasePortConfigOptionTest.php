<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Database;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Database\DatabaseDriverConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\Database\DatabasePortConfigOption;
use Shlinkio\Shlink\Installer\Config\Util\DatabaseDriver;
use Symfony\Component\Console\Style\StyleInterface;

class DatabasePortConfigOptionTest extends TestCase
{
    private DatabasePortConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new DatabasePortConfigOption();
    }

    /** @test */
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('DB_PORT', $this->configOption->getEnvVar());
    }

    /**
     * @test
     * @dataProvider provideCurrentOptions
     */
    public function expectedQuestionIsAsked(array $currentOptions, string $expectedPort): void
    {
        $expectedAnswer = 'the_answer';
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('ask')->with('Database port', $expectedPort)->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io, $currentOptions);

        self::assertEquals($expectedAnswer, $answer);
    }

    public function provideCurrentOptions(): iterable
    {
        $buildCollection = static fn (string $driver): array => [DatabaseDriverConfigOption::ENV_VAR => $driver];

        yield 'mysql' => [$buildCollection(DatabaseDriver::MYSQL->value), '3306'];
        yield 'postgres' => [$buildCollection(DatabaseDriver::POSTGRES->value), '5432'];
        yield 'mssql' => [$buildCollection(DatabaseDriver::MSSQL->value), '1433'];
        yield 'sqlite' => [$buildCollection(DatabaseDriver::SQLITE->value), ''];
        yield 'unsupported' => [$buildCollection('unsupported'), ''];
    }
}
