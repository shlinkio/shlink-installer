<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Service;

use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidator;
use Shlinkio\Shlink\Installer\Model\ImportedConfig;
use Symfony\Component\Console\Style\StyleInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

use function sprintf;

class ShlinkAssetsHandler implements ShlinkAssetsHandlerInterface
{
    public const string GENERATED_CONFIG_PATH = 'config/params/generated_config.php';
    private const array CACHED_CONFIGS_PATHS = ['data/cache/app_config.php', 'data/cache/fastroute_cached_routes.php'];
    private const string SQLITE_DB_PATH = 'data/database.sqlite';
    private const string GEO_LITE_DB_PATH = 'data/GeoLite2-City.mmdb';
    private const string ROAD_RUNNER_BINARY_PATH = 'bin/rr';

    public function __construct(private Filesystem $filesystem)
    {
    }

    /**
     * @throws IOException
     */
    public function dropCachedConfigIfAny(StyleInterface $io): void
    {
        foreach (self::CACHED_CONFIGS_PATHS as $file) {
            $this->dropCachedConfigFile($file, $io);
        }
    }

    private function dropCachedConfigFile(string $file, StyleInterface $io): void
    {
        if (! $this->filesystem->exists($file)) {
            return;
        }

        try {
            $this->filesystem->remove($file);
        } catch (IOException $e) {
            $io->error(
                sprintf('Could not delete cached config! You will have to manually delete the "%s" file.', $file),
            );
            throw $e;
        }
    }

    public function resolvePreviousConfig(StyleInterface $io): ImportedConfig
    {
        $importConfig = $io->confirm(
            'Do you want to import configuration from previous installation? (You will still be asked for any new '
            . 'config option that did not exist in previous shlink versions)',
        );
        if (! $importConfig) {
            return ImportedConfig::notImported();
        }

        do {
            $installationPath = $io->ask(
                'Previous shlink installation path from which to import config',
                validator: static fn ($value) => ConfigOptionsValidator::validateRequired(
                    $value,
                    'previous installation path',
                ),
            );
            $configFile = sprintf('%s/%s', $installationPath, self::GENERATED_CONFIG_PATH);
            $configExists = $this->filesystem->exists($configFile);
        } while (
            ! $configExists &&
            $io->confirm('Provided path does not seem to be a valid shlink root path. Do you want to try another path?')
        );

        // If after some retries the user has chosen not to test another path, return
        if (! $configExists) {
            return ImportedConfig::notImported();
        }

        return ImportedConfig::imported($installationPath, include $configFile);
    }

    public function importShlinkAssetsFromPath(StyleInterface $io, string $path): void
    {
        $this->importSqliteIfNeeded($io, sprintf('%s/%s', $path, self::SQLITE_DB_PATH));
        $this->importGeoLiteDbIfNeeded($io, sprintf('%s/%s', $path, self::GEO_LITE_DB_PATH));
    }

    private function importSqliteIfNeeded(StyleInterface $io, string $fileToImport): void
    {
        if (! $this->filesystem->exists($fileToImport)) {
            return;
        }

        try {
            $this->filesystem->copy($fileToImport, self::SQLITE_DB_PATH);
        } catch (IOException $e) {
            $io->error('It was not possible to import the SQLite database');
            throw $e;
        }
    }

    private function importGeoLiteDbIfNeeded(StyleInterface $io, string $fileToImport): void
    {
        if (! $this->filesystem->exists($fileToImport)) {
            return;
        }

        try {
            $this->filesystem->copy($fileToImport, self::GEO_LITE_DB_PATH);
        } catch (IOException) {
            $io->note('It was not possible to import GeoLite db. Skipping and letting regular update take care of it.');
        }
    }

    public function roadRunnerBinaryExistsInPath(string $path): bool
    {
        return $this->filesystem->exists(sprintf('%s/%s', $path, self::ROAD_RUNNER_BINARY_PATH));
    }
}
