<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Service;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Shlinkio\Shlink\Installer\Service\ShlinkAssetsHandler;
use Symfony\Component\Console\Style\StyleInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

use function Functional\each;
use function strpos;

class ShlinkAssetsHandlerTest extends TestCase
{
    use ProphecyTrait;

    private ShlinkAssetsHandler $assetsHandler;
    private ObjectProphecy $filesystem;
    private ObjectProphecy $io;

    public function setUp(): void
    {
        $this->io = $this->prophesize(StyleInterface::class);
        $this->filesystem = $this->prophesize(Filesystem::class);
        $this->assetsHandler = new ShlinkAssetsHandler($this->filesystem->reveal());
    }

    /**
     * @test
     * @dataProvider provideConfigExists
     */
    public function cachedConfigIsDeletedIfExists(bool $exists, int $expectedRemoveCalls): void
    {
        $appConfigExists = $this->filesystem->exists('data/cache/app_config.php')->willReturn($exists);
        $appConfigRemove = $this->filesystem->remove('data/cache/app_config.php')->willReturn(null);

        $this->assetsHandler->dropCachedConfigIfAny($this->io->reveal());

        $appConfigExists->shouldHaveBeenCalledOnce();
        $appConfigRemove->shouldHaveBeenCalledTimes($expectedRemoveCalls);
    }

    public function provideConfigExists(): iterable
    {
        yield 'no cached config' => [false, 0];
        yield 'cached config' => [true, 1];
    }

    /** @test */
    public function errorWhileDeletingCachedConfigIsPropagated(): void
    {
        $appConfigExists = $this->filesystem->exists('data/cache/app_config.php')->willReturn(true);
        $appConfigRemove = $this->filesystem->remove('data/cache/app_config.php')->willThrow(IOException::class);
        $printError = $this->io->error(
            'Could not delete cached config! You will have to manually delete the "data/cache/app_config.php" file.',
        );

        $appConfigExists->shouldBeCalledOnce();
        $appConfigRemove->shouldBeCalledOnce();
        $printError->shouldBeCalledOnce();
        $this->expectException(IOException::class);

        $this->assetsHandler->dropCachedConfigIfAny($this->io->reveal());
    }

    /** @test */
    public function resolvePreviousConfigDoesNotImportIfUserCancels(): void
    {
        $confirm = $this->io->confirm(
            'Do you want to import configuration from previous installation? (You will still be asked for any new '
            . 'config option that did not exist in previous shlink versions)',
        )->willReturn(false);
        $ask = $this->io->ask(Argument::cetera());

        $this->assetsHandler->resolvePreviousConfig($this->io->reveal());

        $confirm->shouldHaveBeenCalledOnce();
        $ask->shouldNotBeCalled();
    }

    /**
     * @test
     * @dataProvider provideExists
     */
    public function configIsImportedOnlyIfExistingPathIsProvided(bool $exists): void
    {
        $count = 0;
        $importPath = __DIR__ . '/../../test-resources';

        $confirm = $this->io->confirm(Argument::any())->will(function (array $args) use (&$count) {
            [$argument] = $args;

            if (strpos($argument, 'Do you want to import configuration from previous installation?') === 0) {
                return true;
            }

            $count++;
            return $count < 3;
        });
        $ask = $this->io->ask(Argument::cetera())->willReturn($importPath);
        $configExists = $this->filesystem->exists(Argument::containingString($importPath))->willReturn($exists);

        $result = $this->assetsHandler->resolvePreviousConfig($this->io->reveal());

        self::assertEquals($exists ? $importPath : '', $result->importPath());
        $confirm->shouldHaveBeenCalledTimes($exists ? 1 : 4);
        $ask->shouldHaveBeenCalledTimes($exists ? 1 : 3);
        $configExists->shouldHaveBeenCalledTimes($exists ? 1 : 3);
    }

    /**
     * @test
     * @dataProvider provideExists
     */
    public function assetsAreProperlyImportedIfTheyExist(bool $assetsExist): void
    {
        $path = '/foo/bar';
        $assets = ['database.sqlite', 'GeoLite2-City.mmdb'];
        each(
            $assets,
            fn (string $asset) => $this->filesystem->exists($path . '/data/' . $asset)->willReturn($assetsExist),
        );

        $this->assetsHandler->importShlinkAssetsFromPath($this->io->reveal(), $path);

        each($assets, fn (string $asset) => $this->filesystem->copy(
            $path . '/data/' . $asset,
            'data/' . $asset,
        )->shouldHaveBeenCalledTimes($assetsExist ? 1 : 0));
    }

    public function provideExists(): iterable
    {
        yield [true];
        yield [false];
    }

    /** @test */
    public function errorIsThrownIfSqliteImportFails(): void
    {
        $path = '/foo/bar';
        $sqlitePath = $path . '/data/database.sqlite';
        $exists = $this->filesystem->exists($sqlitePath)->willReturn(true);
        $copy = $this->filesystem->copy($sqlitePath, 'data/database.sqlite')->willThrow(IOException::class);
        $error = $this->io->error('It was not possible to import the SQLite database');

        $exists->shouldBeCalledOnce();
        $copy->shouldBeCalledOnce();
        $error->shouldBeCalledOnce();
        $this->expectException(IOException::class);

        $this->assetsHandler->importShlinkAssetsFromPath($this->io->reveal(), $path);
    }

    /** @test */
    public function warningIsPrintedIfGeoliteImportFails(): void
    {
        $path = '/foo/bar';
        $geolitePath = $path . '/data/GeoLite2-City.mmdb';
        $sqliteExists = $this->filesystem->exists($path . '/data/database.sqlite')->willReturn(false);
        $geoliteExists = $this->filesystem->exists($geolitePath)->willReturn(true);
        $copy = $this->filesystem->copy($geolitePath, 'data/GeoLite2-City.mmdb')->willThrow(IOException::class);
        $note = $this->io->note(
            'It was not possible to import GeoLite db. Skipping and letting regular update take care of it.',
        );

        $this->assetsHandler->importShlinkAssetsFromPath($this->io->reveal(), $path);

        $sqliteExists->shouldHaveBeenCalledOnce();
        $geoliteExists->shouldHaveBeenCalledOnce();
        $copy->shouldHaveBeenCalledOnce();
        $note->shouldHaveBeenCalledOnce();
    }
}
