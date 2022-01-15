<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Util;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\UrlShortener\ShortDomainSchemaConfigOption;
use Shlinkio\Shlink\Installer\Util\Utils;

class UtilsTest extends TestCase
{
    /** @test */
    public function normalizeAndKeepEnvVarKeysReturnsExpectedValue(): void
    {
        self::assertEquals([
            'ENV_VAR' => 0,
            'JARL' => '123',
            'AS_ARRAY' => 'foo,bar,baz',
            'REGULAR_404_REDIRECT' => 'this is kept',
            ShortDomainSchemaConfigOption::ENV_VAR => true,
        ], Utils::normalizeAndKeepEnvVarKeys([
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
        ]));
    }

    /**
     * @test
     * @dataProvider provideCommaSeparatedLists
     */
    public function commaSeparatedToListReturnsExpectedValue(string $list, array $expectedResult): void
    {
        self::assertEquals($expectedResult, Utils::commaSeparatedToList($list));
    }

    public function provideCommaSeparatedLists(): iterable
    {
        yield 'single item' => ['foo', ['foo']];
        yield 'multiple items' => ['foo,bar bar,baz', ['foo', 'bar bar', 'baz']];
        yield 'extra spaces' => ['  foo ,  bar   ,  baz ', ['foo', 'bar', 'baz']];
    }
}
