<?php
declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Util;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Util\PathCollection;

class PathCollectionTest extends TestCase
{
    /** @var PathCollection */
    private $collection;

    public function setUp(): void
    {
        $this->collection = new PathCollection([
            'foo' => [
                'bar' => [
                    'baz' => 'Hello world!',
                ],
            ],
            'something' => [],
            'another' => [
                'one' => 'Shlink',
            ],
        ]);
    }

    /**
     * @test
     * @dataProvider providePaths
     */
    public function pathExistsReturnsExpectedValue(array $path, bool $expected)
    {
        $this->assertEquals($expected, $this->collection->pathExists($path));
    }

    public function providePaths(): iterable
    {
        yield [[], false];
        yield [['boo'], false];
        yield [['foo', 'nop'], false];
        yield [['another', 'one', 'nop'], false];
        yield [['foo'], true];
        yield [['foo', 'bar'], true];
        yield [['foo', 'bar', 'baz'], true];
        yield [['something'], true];
    }

    /**
     * @test
     * @dataProvider providePathsWithValue
     */
    public function getValueInPathReturnsExpectedValue(array $path, $expected)
    {
        $this->assertEquals($expected, $this->collection->getValueInPath($path));
    }

    public function providePathsWithValue(): iterable
    {
        yield [[], null];
        yield [['boo'], null];
        yield [['foo', 'nop'], null];
        yield [['another', 'one', 'nop'], null];
        yield [['foo'], [
            'bar' => [
                'baz' => 'Hello world!',
            ],
        ]];
        yield [['foo', 'bar'], [
            'baz' => 'Hello world!',
        ]];
        yield [['foo', 'bar', 'baz'], 'Hello world!'];
        yield [['something'], []];
    }
}
