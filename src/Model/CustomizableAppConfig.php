<?php
declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Model;

use Shlinkio\Shlink\Installer\Config\Plugin\ApplicationConfigCustomizer;
use Shlinkio\Shlink\Installer\Config\Plugin\DatabaseConfigCustomizer;
use Shlinkio\Shlink\Installer\Config\Plugin\LanguageConfigCustomizer;
use Shlinkio\Shlink\Installer\Config\Plugin\UrlShortenerConfigCustomizer;
use Shlinkio\Shlink\Installer\Util\PathCollection;
use Zend\Stdlib\ArraySerializableInterface;

final class CustomizableAppConfig implements ArraySerializableInterface
{
    public const SQLITE_DB_PATH = 'data/database.sqlite';

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

        $this->setApp($this->mapExistingPathsToKeys([
            ApplicationConfigCustomizer::SECRET => ['app_options', 'secret_key'],
            ApplicationConfigCustomizer::DISABLE_TRACK_PARAM => ['app_options', 'disable_track_param'],
            ApplicationConfigCustomizer::CHECK_VISITS_THRESHOLD => ['delete_short_urls', 'check_visits_threshold'],
            ApplicationConfigCustomizer::VISITS_THRESHOLD => ['delete_short_urls', 'visits_threshold'],
        ], $pathCollection));

        $this->setDatabase($this->mapExistingPathsToKeys([
            DatabaseConfigCustomizer::DRIVER => ['entity_manager', 'connection', 'driver'],
            DatabaseConfigCustomizer::USER => ['entity_manager', 'connection', 'user'],
            DatabaseConfigCustomizer::PASSWORD => ['entity_manager', 'connection', 'password'],
            DatabaseConfigCustomizer::NAME => ['entity_manager', 'connection', 'dbname'],
            DatabaseConfigCustomizer::HOST => ['entity_manager', 'connection', 'host'],
            DatabaseConfigCustomizer::PORT => ['entity_manager', 'connection', 'port'],
        ], $pathCollection));

        $this->setLanguage($this->mapExistingPathsToKeys([
            LanguageConfigCustomizer::DEFAULT_LANG => ['translator', 'locale'],
        ], $pathCollection));

        $this->setUrlShortener($this->mapExistingPathsToKeys([
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
        ], $pathCollection));
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
        $app = [
            'secret_key' => $this->app[ApplicationConfigCustomizer::SECRET] ?? '',
            'disable_track_param' => $this->app[ApplicationConfigCustomizer::DISABLE_TRACK_PARAM] ?? null,
        ];
        $deleteShortUrls = [
            'check_visits_threshold' => $this->app[ApplicationConfigCustomizer::CHECK_VISITS_THRESHOLD] ?? true,
            'visits_threshold' => $this->app[ApplicationConfigCustomizer::VISITS_THRESHOLD] ?? 15,
        ];
        $connection = $this->buildConnectionConfig();
        $translator = [
            'locale' => $this->language[LanguageConfigCustomizer::DEFAULT_LANG] ?? 'en',
        ];
        $urlShortener = [
            'domain' => [
                'schema' => $this->urlShortener[UrlShortenerConfigCustomizer::SCHEMA] ?? 'http',
                'hostname' => $this->urlShortener[UrlShortenerConfigCustomizer::HOSTNAME] ?? '',
            ],
            'shortcode_chars' => $this->urlShortener[UrlShortenerConfigCustomizer::CHARS] ?? '',
            'validate_url' => $this->urlShortener[UrlShortenerConfigCustomizer::VALIDATE_URL] ?? true,
        ];
        $notFoundShortUrl = [
            'enable_redirection' =>
                $this->urlShortener[UrlShortenerConfigCustomizer::ENABLE_NOT_FOUND_REDIRECTION] ?? false,
            'redirect_to' => $this->urlShortener[UrlShortenerConfigCustomizer::NOT_FOUND_REDIRECT_TO] ?? null,
        ];
        $urlShortener['not_found_short_url'] = $notFoundShortUrl;

        return [
            'app_options' => $app,
            'delete_short_urls' => $deleteShortUrls,
            'entity_manager' => ['connection' => $connection],
            'translator' => $translator,
            'url_shortener' => $urlShortener,
        ];
    }

    private function buildConnectionConfig(): array
    {
        $dbDriver = $this->database[DatabaseConfigCustomizer::DRIVER] ?? '';
        $connection = ['driver' => $dbDriver];
        // Build dynamic database config based on selected driver
        if ($dbDriver === 'pdo_sqlite') {
            $connection['path'] = self::SQLITE_DB_PATH;
        } else {
            $connection['user'] = $this->database[DatabaseConfigCustomizer::USER] ?? '';
            $connection['password'] = $this->database[DatabaseConfigCustomizer::PASSWORD] ?? '';
            $connection['dbname'] = $this->database[DatabaseConfigCustomizer::NAME] ?? '';
            $connection['host'] = $this->database[DatabaseConfigCustomizer::HOST] ?? '';
            $connection['port'] = $this->database[DatabaseConfigCustomizer::PORT] ?? '';

            if ($dbDriver === 'pdo_mysql') {
                $connection['driverOptions'] = [
                    // PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                    1002 => 'SET NAMES utf8',
                ];
            }
        }

        return $connection;
    }
}
