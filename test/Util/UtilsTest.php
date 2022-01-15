<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Util;

use PHPUnit\Framework\TestCase;
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
        ]));
    }
}
