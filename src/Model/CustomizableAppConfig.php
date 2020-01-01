<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Model;

use Shlinkio\Shlink\Installer\Config\Plugin\DatabaseConfigCustomizer;
use Shlinkio\Shlink\Installer\Util\PathCollection;
use Zend\Stdlib\ArraySerializableInterface;

use function array_key_exists;

final class CustomizableAppConfig implements ArraySerializableInterface
{
    public const SQLITE_DB_PATH = 'data/database.sqlite';

    private const APP_CONFIG_MAP = [
//        Option\DisableTrackParamConfigOption::class => ['app_options', 'disable_track_param'],
//        Option\BasePathConfigOption::class => ['router', 'base_path'],
//        Option\CheckVisitsThresholdConfigOption::class => ['delete_short_urls', 'check_visits_threshold'],
//        Option\VisitsThresholdConfigOption::class => ['delete_short_urls', 'visits_threshold'],
//        Option\WebWorkerNumConfigOption::class => ['web_worker_num'], // Using simplified config
//        Option\TaskWorkerNumConfigOption::class => ['task_worker_num'], // Using simplified config
    ];
    private const DB_CONFIG_MAP = [
//        Option\DatabaseDriverConfigOption::class => ['entity_manager', 'connection', 'driver'],
//        Option\DatabaseUserConfigOption::class => ['entity_manager', 'connection', 'user'],
//        Option\DatabasePasswordConfigOption::class => ['entity_manager', 'connection', 'password'],
//        Option\DatabaseNameConfigOption::class => ['entity_manager', 'connection', 'dbname'],
//        Option\DatabaseHostConfigOption::class => ['entity_manager', 'connection', 'host'],
//        Option\DatabasePortConfigOption::class => ['entity_manager', 'connection', 'port'],
    ];
    private const URL_SHORTENER_CONFIG_MAP = [
//        Option\ShortDomainSchemaConfigOption::class => ['url_shortener', 'domain', 'schema'],
//        Option\ShortDomainHostConfigOption::class => ['url_shortener', 'domain', 'hostname'],
//        Option\ValidateUrlConfigOption::class => ['url_shortener', 'validate_url'],
//        Option\VisitsWebhooksConfigOption::class => ['url_shortener', 'visits_webhooks'],
    ];
    private const REDIRECTS_CONFIG_MAP = [
//        Option\InvalidShortUrlRedirectConfigOption::class => ['not_found_redirects', 'invalid_short_url'],
//        Option\Regular404RedirectConfigOption::class => ['not_found_redirects', 'regular_404'],
//        Option\BaseUrlRedirectConfigOption::class => ['not_found_redirects', 'base_url'],
    ];

    /** @var array */
    private $database = [];
    /** @var array */
    private $urlShortener = [];
    /** @var array */
    private $app = [];
    /** @var array */
    private $redirects = [];
    /** @var string|null */
    private $importedInstallationPath;

    public function getDatabase(): array
    {
        return $this->database;
    }

    public function setDatabase(array $database): self
    {
        $this->database = $database;
        return $this;
    }

    public function hasDatabase(): bool
    {
        return ! empty($this->database);
    }

    public function getUrlShortener(): array
    {
        return $this->urlShortener;
    }

    public function setUrlShortener(array $urlShortener): self
    {
        $this->urlShortener = $urlShortener;
        return $this;
    }

    public function hasUrlShortener(): bool
    {
        return ! empty($this->urlShortener);
    }

    public function getApp(): array
    {
        return $this->app;
    }

    public function setApp(array $app): self
    {
        $this->app = $app;
        return $this;
    }

    public function hasApp(): bool
    {
        return ! empty($this->app);
    }

    public function getRedirects(): array
    {
        return $this->redirects;
    }

    public function setRedirects(array $redirects): self
    {
        $this->redirects = $redirects;
        return $this;
    }

    public function hasRedirects(): bool
    {
        return ! empty($this->redirects);
    }

    public function getImportedInstallationPath(): ?string
    {
        return $this->importedInstallationPath;
    }

    public function setImportedInstallationPath(string $importedInstallationPath): self
    {
        $this->importedInstallationPath = $importedInstallationPath;
        return $this;
    }

    public function hasImportedInstallationPath(): bool
    {
        return $this->importedInstallationPath !== null;
    }

    public function exchangeArray(array $array): void
    {
        $pathCollection = new PathCollection($array);

        $this->setApp($this->mapExistingPathsToKeys(self::APP_CONFIG_MAP, $pathCollection));
        $this->setDatabase($this->mapExistingPathsToKeys(self::DB_CONFIG_MAP, $pathCollection));
        $this->setUrlShortener($this->mapExistingPathsToKeys(self::URL_SHORTENER_CONFIG_MAP, $pathCollection));
        $this->setRedirects($this->mapExistingPathsToKeys(self::REDIRECTS_CONFIG_MAP, $pathCollection));
    }

    private function mapExistingPathsToKeys(array $map, PathCollection $pathCollection): array
    {
        $result = [];
        foreach ($map as $key => $path) {
            if ($pathCollection->pathExists($path)) {
                $result[$key] = $pathCollection->getValueInPath($path);
            }
        }

        return $result;
    }

    public function getArrayCopy(): array
    {
        $pathCollection = new PathCollection();

        $this->mapExistingKeysToPaths(self::APP_CONFIG_MAP, $this->app, $pathCollection);
        $this->buildConnectionConfig($pathCollection);
        $this->mapExistingKeysToPaths(self::URL_SHORTENER_CONFIG_MAP, $this->urlShortener, $pathCollection);
        $this->mapExistingKeysToPaths(self::REDIRECTS_CONFIG_MAP, $this->redirects, $pathCollection);

        return $pathCollection->toArray();
    }

    private function buildConnectionConfig(PathCollection $pathCollection): void
    {
        $dbDriver = $this->database[DatabaseConfigCustomizer::DRIVER] ?? '';
        $this->mapExistingKeysToPaths(self::DB_CONFIG_MAP, $this->database, $pathCollection);

        // Build dynamic database config based on selected driver
        if ($dbDriver === 'pdo_sqlite') {
            $pathCollection->setValueInPath(self::SQLITE_DB_PATH, ['entity_manager', 'connection', 'path']);
        } elseif ($dbDriver === 'pdo_mysql') {
            $pathCollection->setValueInPath([
                // PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                1002 => 'SET NAMES utf8',
            ], ['entity_manager', 'connection', 'driverOptions']);
        }
    }

    private function mapExistingKeysToPaths(array $map, array $sourceConfig, PathCollection $pathCollection): void
    {
        foreach ($map as $key => $path) {
            if (! array_key_exists($key, $sourceConfig)) {
                continue;
            }

            $pathCollection->setValueInPath($sourceConfig[$key], $path);
        }
    }
}
