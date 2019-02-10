<?php
declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Model;

use Shlinkio\Shlink\Installer\Config\Plugin\ApplicationConfigCustomizer;
use Shlinkio\Shlink\Installer\Config\Plugin\DatabaseConfigCustomizer;
use Shlinkio\Shlink\Installer\Config\Plugin\LanguageConfigCustomizer;
use Shlinkio\Shlink\Installer\Config\Plugin\UrlShortenerConfigCustomizer;
use Shlinkio\Shlink\Installer\Util\PathCollection;
use Zend\Stdlib\ArraySerializableInterface;
use Zend\Stdlib\ArrayUtils;
use function array_key_exists;
use function array_reduce;
use function array_shift;
use function count;

final class CustomizableAppConfig implements ArraySerializableInterface
{
    public const SQLITE_DB_PATH = 'data/database.sqlite';

    private const APP_CONFIG_MAP = [
        ApplicationConfigCustomizer::SECRET => ['app_options', 'secret_key'],
        ApplicationConfigCustomizer::DISABLE_TRACK_PARAM => ['app_options', 'disable_track_param'],
        ApplicationConfigCustomizer::CHECK_VISITS_THRESHOLD => ['delete_short_urls', 'check_visits_threshold'],
        ApplicationConfigCustomizer::VISITS_THRESHOLD => ['delete_short_urls', 'visits_threshold'],
    ];
    private const DB_CONFIG_MAP = [
        DatabaseConfigCustomizer::DRIVER => ['entity_manager', 'connection', 'driver'],
        DatabaseConfigCustomizer::USER => ['entity_manager', 'connection', 'user'],
        DatabaseConfigCustomizer::PASSWORD => ['entity_manager', 'connection', 'password'],
        DatabaseConfigCustomizer::NAME => ['entity_manager', 'connection', 'dbname'],
        DatabaseConfigCustomizer::HOST => ['entity_manager', 'connection', 'host'],
        DatabaseConfigCustomizer::PORT => ['entity_manager', 'connection', 'port'],
    ];
    private const LANG_CONFIG_MAP = [
        LanguageConfigCustomizer::DEFAULT_LANG => ['translator', 'locale'],
    ];
    private const URL_SHORTENER_CONFIG_MAP = [
        UrlShortenerConfigCustomizer::SCHEMA => ['url_shortener', 'domain', 'schema'],
        UrlShortenerConfigCustomizer::HOSTNAME => ['url_shortener', 'domain', 'hostname'],
        UrlShortenerConfigCustomizer::CHARS => ['url_shortener', 'shortcode_chars'],
        UrlShortenerConfigCustomizer::VALIDATE_URL => ['url_shortener', 'validate_url'],
        UrlShortenerConfigCustomizer::ENABLE_NOT_FOUND_REDIRECTION => [
            'url_shortener',
            'not_found_short_url',
            'enable_redirection',
        ],
        UrlShortenerConfigCustomizer::NOT_FOUND_REDIRECT_TO => [
            'url_shortener',
            'not_found_short_url',
            'redirect_to',
        ],
    ];

    /** @var array */
    private $database = [];
    /** @var array */
    private $urlShortener = [];
    /** @var array */
    private $language = [];
    /** @var array */
    private $app = [];
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

    public function getLanguage(): array
    {
        return $this->language;
    }

    public function setLanguage(array $language): self
    {
        $this->language = $language;
        return $this;
    }

    public function hasLanguage(): bool
    {
        return ! empty($this->language);
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
        $this->setLanguage($this->mapExistingPathsToKeys(self::LANG_CONFIG_MAP, $pathCollection));
        $this->setUrlShortener($this->mapExistingPathsToKeys(self::URL_SHORTENER_CONFIG_MAP, $pathCollection));
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
        $app = $this->mapExistingKeysToPaths(self::APP_CONFIG_MAP, $this->app);
        $db = $this->buildConnectionConfig();
        $translator = $this->mapExistingKeysToPaths(self::LANG_CONFIG_MAP, $this->language);
        $urlShortener = $this->mapExistingKeysToPaths(self::URL_SHORTENER_CONFIG_MAP, $this->urlShortener);

        return array_reduce([$app, $db, $translator, $urlShortener], [ArrayUtils::class, 'merge'], []);
    }

    private function buildConnectionConfig(): array
    {
        $dbDriver = $this->database[DatabaseConfigCustomizer::DRIVER] ?? '';
        $db = $this->mapExistingKeysToPaths(self::DB_CONFIG_MAP, $this->database);

        // Build dynamic database config based on selected driver
        if ($dbDriver === 'pdo_sqlite') {
            $db['entity_manager']['connection']['path'] = self::SQLITE_DB_PATH;
        } elseif ($dbDriver === 'pdo_mysql') {
            $db['entity_manager']['connection']['driverOptions'] = [
                // PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                1002 => 'SET NAMES utf8',
            ];
        }

        return $db;
    }

    private function mapExistingKeysToPaths(array $map, array $sourceConfig): array
    {
        $result = [];

        foreach ($map as $key => $path) {
            if (! array_key_exists($key, $sourceConfig)) {
                continue;
            }

            $value = $sourceConfig[$key];
            $ref =& $result;
            while (count($path) > 1) {
                $currentKey = array_shift($path);
                if (! array_key_exists($currentKey, $ref)) {
                    $ref[$currentKey] = [];
                }

                $ref =& $ref[$currentKey];
            }
            $ref[array_shift($path)] = $value;
        }

        return $result;
    }
}
