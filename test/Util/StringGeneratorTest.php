<?php
declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Util;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Util\StringGenerator;
use function random_int;
use function strlen;

class StringGeneratorTest extends TestCase
{
    /** @var StringGenerator */
    private $stringGenerator;

    public function setUp(): void
    {
        $this->stringGenerator = new StringGenerator();
    }

    /**
     * @test
     * @dataProvider provideSizes
     */
    public function generatesRandomStringOfExpectedSize(int $size): void
    {
        $generated = $this->stringGenerator->generateRandomString($size);
        $this->assertEquals($size, strlen($generated));
    }

    public function provideSizes(): array
    {
        return [
            [1],
            [10],
            [33],
            [random_int(5, 50)],
        ];
    }
}
