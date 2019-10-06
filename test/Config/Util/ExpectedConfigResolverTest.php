<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Util;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Util\ExpectedConfigResolver;

class ExpectedConfigResolverTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideConfigs
     */
    public function properlyResolvesConfig(array $keysMap, string $name, ?array $default, array $expected): void
    {
        $resolver = new ExpectedConfigResolver($keysMap);

        $this->assertEquals($expected, $resolver->resolveExpectedKeys($name, $default));
    }

    public function provideConfigs(): iterable
    {
        yield 'existing name without default' => [
            ['foo' => ['foo', 'bar']],
            'foo',
            null,
            ['foo', 'bar'],
        ];
        yield 'existing name with default' => [
            ['foo' => ['foo', 'bar']],
            'foo',
            ['baz' => 'foo'],
            ['foo', 'bar'],
        ];
        yield 'non-existing name with default' => [
            ['foo' => ['foo', 'bar']],
            'invalid',
            ['baz' => 'foo'],
            ['baz' => 'foo'],
        ];
        yield 'non-existing name without default' => [
            ['foo' => ['foo', 'bar']],
            'invalid',
            null,
            [],
        ];
    }
}
