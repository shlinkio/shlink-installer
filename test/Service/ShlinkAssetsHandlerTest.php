<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Service;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Rule\InvocationOrder;
use PHPUnit\Framework\MockObject\Rule\InvokedCount;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Service\ShlinkAssetsHandler;
use Symfony\Component\Console\Style\StyleInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

use function array_map;
use function str_starts_with;

#[AllowMockObjectsWithoutExpectations]
class ShlinkAssetsHandlerTest extends TestCase
{
    private ShlinkAssetsHandler $assetsHandler;
    private MockObject & StyleInterface $io;
    private MockObject & Filesystem $filesystem;

    public function setUp(): void
    {
        $this->io = $this->createMock(StyleInterface::class);
        $this->filesystem = $this->createMock(Filesystem::class);
        $this->assetsHandler = new ShlinkAssetsHandler($this->filesystem);
    }

    #[Test, DataProvider('provideConfigExists')]
    public function cachedConfigIsDeletedIfExists(bool $appExists, bool $routesExist, int $expectedRemoveCalls): void
    {
        $this->filesystem->expects($this->exactly(2))->method('exists')->willReturnMap([
            ['data/cache/app_config.php', $appExists],
            ['data/cache/fastroute_cached_routes.php', $routesExist],
        ]);
        $this->filesystem->expects($this->exactly($expectedRemoveCalls))->method('remove')->with(
            $this->stringContains('data/cache'),
        );

        $this->assetsHandler->dropCachedConfigIfAny($this->io);
    }

    public static function provideConfigExists(): iterable
    {
        yield 'no cached app or route config' => [false, false, 0];
        yield 'cached app config' => [true, false, 1];
        yield 'cached route config' => [false, true, 1];
        yield 'both configs cached' => [true, true, 2];
    }

    #[Test]
    public function errorWhileDeletingCachedConfigIsPropagated(): void
    {
        $this->filesystem->expects($this->once())->method('exists')->with('data/cache/app_config.php')->willReturn(
            true,
        );
        $this->filesystem->expects($this->once())->method('remove')->with(
            'data/cache/app_config.php',
        )->willThrowException(new IOException(''));
        $this->io->expects($this->once())->method('error')->with(
            'Could not delete cached config! You will have to manually delete the "data/cache/app_config.php" file.',
        );
        $this->expectException(IOException::class);

        $this->assetsHandler->dropCachedConfigIfAny($this->io);
    }

    #[Test]
    public function resolvePreviousConfigDoesNotImportIfUserCancels(): void
    {
        $this->io->expects($this->once())->method('confirm')->with(
            'Do you want to import configuration from previous installation? (You will still be asked for any new '
            . 'config option that did not exist in previous shlink versions)',
        )->willReturn(false);
        $this->io->expects($this->never())->method('ask');

        $this->assetsHandler->resolvePreviousConfig($this->io);
    }

    #[Test, DataProvider('provideExists')]
    public function configIsImportedOnlyIfExistingPathIsProvided(bool $exists, mixed $_): void
    {
        $count = 0;
        $importPath = __DIR__ . '/../../test-resources';

        $this->io->expects($this->exactly($exists ? 1 : 4))->method('confirm')->willReturnCallback(
            function (string $argument) use (&$count) {
                if (str_starts_with($argument, 'Do you want to import configuration from previous installation?')) {
                    return true;
                }

                $count++;
                return $count < 3;
            },
        );
        $this->io->expects($this->exactly($exists ? 1 : 3))->method('ask')->willReturn($importPath);
        $this->filesystem->expects($this->exactly($exists ? 1 : 3))->method('exists')->with(
            $this->stringContains($importPath),
        )->willReturn($exists);

        $result = $this->assetsHandler->resolvePreviousConfig($this->io);

        self::assertEquals($exists ? $importPath : '', $result->importPath);
    }

    #[Test, DataProvider('provideExists')]
    public function assetsAreProperlyImportedIfTheyExist(bool $assetsExist, InvocationOrder $expectedCopies): void
    {
        $path = '/foo/bar';
        $assets = ['database.sqlite', 'GeoLite2-City.mmdb'];
        $this->filesystem->expects($this->exactly(2))->method('exists')->willReturnMap(array_map(
            fn (string $asset) => [$path . '/data/' . $asset, $assetsExist],
            $assets,
        ));
        $this->filesystem->expects($expectedCopies)->method('copy')->withAnyParameters();

        $this->assetsHandler->importShlinkAssetsFromPath($this->io, $path);
    }

    public static function provideExists(): iterable
    {
        yield [true, new InvokedCount(2)];
        yield [false, new InvokedCount(0)];
    }

    #[Test]
    public function errorIsThrownIfSqliteImportFails(): void
    {
        $path = '/foo/bar';
        $sqlitePath = $path . '/data/database.sqlite';
        $this->filesystem->expects($this->once())->method('exists')->with($sqlitePath)->willReturn(true);
        $this->filesystem->expects($this->once())->method('copy')->with(
            $sqlitePath,
            'data/database.sqlite',
        )->willThrowException(new IOException(''));
        $this->io->expects($this->once())->method('error')->with('It was not possible to import the SQLite database');

        $this->expectException(IOException::class);

        $this->assetsHandler->importShlinkAssetsFromPath($this->io, $path);
    }

    #[Test]
    public function warningIsPrintedIfGeoliteImportFails(): void
    {
        $path = '/foo/bar';
        $geolitePath = $path . '/data/GeoLite2-City.mmdb';
        $this->filesystem->expects($this->exactly(2))->method('exists')->willReturnMap([
            [$path . '/data/database.sqlite', false],
            [$geolitePath, true],
        ]);
        $this->filesystem->expects($this->once())->method('copy')->with(
            $geolitePath,
            'data/GeoLite2-City.mmdb',
        )->willThrowException(new IOException(''));
        $this->io->expects($this->once())->method('note')->with(
            'It was not possible to import GeoLite db. Skipping and letting regular update take care of it.',
        );

        $this->assetsHandler->importShlinkAssetsFromPath($this->io, $path);
    }

    #[Test, DataProvider('providePaths')]
    public function roadRunnerBinaryExistsInPathChecksExpectedFile(string $path, bool $expectedResult): void
    {
        $this->filesystem->expects($this->once())->method('exists')->with($path . '/bin/rr')->willReturn(
            $expectedResult,
        );

        $result = $this->assetsHandler->roadRunnerBinaryExistsInPath($path);

        self::assertEquals($expectedResult, $result);
    }

    public static function providePaths(): iterable
    {
        yield ['foo', true];
        yield ['foo/bar', false];
        yield ['foo/bar/baz', true];
    }
}
