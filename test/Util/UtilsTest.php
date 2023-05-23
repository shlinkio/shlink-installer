<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Util;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Database\DatabaseDriverConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\UrlShortener\ShortDomainSchemaConfigOption;
use Shlinkio\Shlink\Installer\Util\Utils;

class UtilsTest extends TestCase
{
    #[Test, DataProvider('provideEnvVars')]
    public function normalizeAndKeepEnvVarKeysReturnsExpectedValue(array $input, array $expected): void
    {
        self::assertEquals($expected, Utils::normalizeAndKeepEnvVarKeys($input));
    }

    public static function provideEnvVars(): iterable
    {
        yield [[
            'foo' => [
                'bar',
            ],
            'ENV_VAR' => 0,
            'baz' => [],
            'JARL' => '123',
            'ignored' => [
                'foo' => [
                    'bar' => 'baz',
                ],
            ],
            'AS_ARRAY' => ['foo', 'bar', 'baz'],
            'REGULAR_404_REDIRECT' => 'this is kept',
            ShortDomainSchemaConfigOption::ENV_VAR => 'https',
        ], [
            'ENV_VAR' => 0,
            'JARL' => '123',
            'AS_ARRAY' => 'foo,bar,baz',
            'REGULAR_404_REDIRECT' => 'this is kept',
            ShortDomainSchemaConfigOption::ENV_VAR => true,
        ]];
        yield [[ShortDomainSchemaConfigOption::ENV_VAR => 'http'], [ShortDomainSchemaConfigOption::ENV_VAR => false]];
        yield [
            [DatabaseDriverConfigOption::ENV_VAR => 'pdo_pgsql'],
            [DatabaseDriverConfigOption::ENV_VAR => 'postgres'],
        ];
        yield [
            [DatabaseDriverConfigOption::ENV_VAR => 'pdo_sqlite'],
            [DatabaseDriverConfigOption::ENV_VAR => 'sqlite'],
        ];
        yield [
            [DatabaseDriverConfigOption::ENV_VAR => 'pdo_sqlsrv'],
            [DatabaseDriverConfigOption::ENV_VAR => 'mssql'],
        ];
        yield [
            [DatabaseDriverConfigOption::ENV_VAR => 'pdo_mysql'],
            [DatabaseDriverConfigOption::ENV_VAR => 'mysql'],
        ];
    }

    #[Test, DataProvider('provideCommaSeparatedLists')]
    public function commaSeparatedToListReturnsExpectedValue(string $list, array $expectedResult): void
    {
        self::assertEquals($expectedResult, Utils::commaSeparatedToList($list));
    }

    public static function provideCommaSeparatedLists(): iterable
    {
        yield 'single item' => ['foo', ['foo']];
        yield 'multiple items' => ['foo,bar bar,baz', ['foo', 'bar bar', 'baz']];
        yield 'extra spaces' => ['  foo ,  bar   ,  baz ', ['foo', 'bar', 'baz']];
    }
}
