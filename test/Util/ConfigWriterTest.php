<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Util;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Util\ConfigWriter;

use function unlink;

class ConfigWriterTest extends TestCase
{
    private const FILENAME = __DIR__ . '/../../test-resources/config/params/generated-in-test.php';

    private ConfigWriter $configWriter;

    protected function setUp(): void
    {
        $this->configWriter = new ConfigWriter();
    }

    public static function tearDownAfterClass(): void
    {
        unlink(self::FILENAME);
    }

    #[Test]
    #[TestWith([[
        'foo' => 'foo',
        'bar' => 'bar',
        'baz' => 'baz',
    ]])]
    #[TestWith([[
        'foo' => null,
        'bar' => 123,
        'baz' => true,
    ]])]
    public function configIsExportedAndWrittenToFile(array $config): void
    {
        $this->configWriter->toFile(self::FILENAME, $config);

        self::assertFileExists(self::FILENAME);
        self::assertEquals($config, require self::FILENAME);
    }
}
