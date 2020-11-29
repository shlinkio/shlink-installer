# CHANGELOG

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com), and this project adheres to [Semantic Versioning](https://semver.org).

## [5.2.0] - 2020-11-29
### Added
* Added explicit support for PHP 8
* Added support for unix sockets on MySQL, MariaDB and PostgreSQL databases

### Changed
* Updated to `infection` 0.20.

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*


## [5.1.1] - 2020-10-25
### Added
* *Nothing*

### Changed
* Added PHP 8 to the build matrix, allowing failures on it.
* Updated to composer 2.

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*


## [5.1.0] - 2020-06-20
### Added
* [#87](https://github.com/shlinkio/shlink-installer/issues/87) Added config options for redirect customization.

### Changed
* *Nothing*

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*


## [5.0.0] - 2020-05-09
### Added
* [#71](https://github.com/shlinkio/shlink-installer/issues/71) Added config options for mercure integration.
* [#82](https://github.com/shlinkio/shlink-installer/issues/82) Added config option to optionally disable IP address anonymization.
* [#76](https://github.com/shlinkio/shlink-installer/issues/76) Added `MYSQL_ATTR_USE_BUFFERED_QUERY => true` driver option for mysql and mariadb connections.

### Changed
* [#74](https://github.com/shlinkio/shlink-installer/issues/74) Grouped several config options to sub-namespaces. The changed classes are as follows.

    * `Database`:
        * `Config\Option\DatabaseDriverConfigOption` -> `Config\Option\Database\DatabaseDriverConfigOption`
        * `Config\Option\DatabaseNameConfigOption` -> `Config\Option\Database\DatabaseNameConfigOption`
        * `Config\Option\DatabaseHostConfigOption` -> `Config\Option\Database\DatabaseHostConfigOption`
        * `Config\Option\DatabasePortConfigOption` -> `Config\Option\Database\DatabasePortConfigOption`
        * `Config\Option\DatabaseUserConfigOption` -> `Config\Option\Database\DatabaseUserConfigOption`
        * `Config\Option\DatabasePasswordConfigOption` -> `Config\Option\Database\DatabasePasswordConfigOption`
        * `Config\Option\DatabaseSqlitePathConfigOption` -> `Config\Option\Database\DatabaseSqlitePathConfigOption`
        * `Config\Option\DatabaseMySqlOptionsConfigOption` -> `Config\Option\Database\DatabaseMySqlOptionsConfigOption`
    * `UrlShortener`:
        * `Config\Option\ShortDomainHostConfigOption` -> `Config\Option\UrlShortener\ShortDomainHostConfigOption`
        * `Config\Option\ShortDomainSchemaConfigOption` -> `Config\Option\UrlShortener\ShortDomainSchemaConfigOption`
        * `Config\Option\ValidateUrlConfigOption` -> `Config\Option\UrlShortener\ValidateUrlConfigOption`
        * `Config\Option\ShortCodeLengthOption` -> `Config\Option\UrlShortener\ShortCodeLengthOption`
    * `Visit`:
        * `Config\Option\VisitsWebhooksConfigOption` -> `Config\Option\Visit\VisitsWebhooksConfigOption`
        * `Config\Option\CheckVisitsThresholdConfigOption` -> `Config\Option\Visit\CheckVisitsThresholdConfigOption`
        * `Config\Option\VisitsThresholdConfigOption` -> `Config\Option\Visit\VisitsThresholdConfigOption`
    * `Redirect`:
        * `Config\Option\BaseUrlRedirectConfigOption` -> `Config\Option\Redirect\BaseUrlRedirectConfigOption`
        * `Config\Option\InvalidShortUrlRedirectConfigOption` -> `Config\Option\Redirect\InvalidShortUrlRedirectConfigOption`
        * `Config\Option\Regular404RedirectConfigOption` -> `Config\Option\Redirect\Regular404RedirectConfigOption`
    * `Worker`:
        * `Config\Option\TaskWorkerNumConfigOption` -> `Config\Option\Worker\TaskWorkerNumConfigOption`
        * `Config\Option\WebWorkerNumConfigOption` -> `Config\Option\Worker\WebWorkerNumConfigOption`

    The rest remain the same.

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* [#78](https://github.com/shlinkio/shlink-installer/issues/78) Allowed clear cache command to fail, and ensured it is not run on new installs.


## [4.4.0] - 2020-04-29
### Added
* [#80](https://github.com/shlinkio/shlink-installer/issues/80) Added config option to ask for a custom GeoLite license key.

### Changed
* *Nothing*

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*


## [4.3.2] - 2020-04-06
### Added
* *Nothing*

### Changed
* [#68](https://github.com/shlinkio/shlink-installer/issues/68) Updated dev dependencies.

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* [#69](https://github.com/shlinkio/shlink-installer/issues/69) Ensured `doctrine orm:clear-cache:meta` command is run after the installation, to avoid outdated cached metadata to be persisted between versions.


## [4.3.1] - 2020-03-23
### Added
* *Nothing*

### Changed
* *Nothing*

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* [#66](https://github.com/shlinkio/shlink-installer/issues/66) Fixed regression making config not to be loaded from proper location.


## [4.3.0] - 2020-03-13
### Added
* *Nothing*

### Changed
* [#64](https://github.com/shlinkio/shlink-installer/issues/64) Added `shlinkio/shlink-config` as a project dependency, deprecating the `Shlinkio\Shlink\Installer\Utils\PathCollection` class.

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*


## [4.2.0] - 2020-02-18
### Added
* [#62](https://github.com/shlinkio/shlink-installer/issues/62) Added config option to ask for default short codes length.

### Changed
* *Nothing*

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*


## [4.1.0] - 2020-02-15
### Added
* [#56](https://github.com/shlinkio/shlink-installer/issues/56) Added MicrosoftSQL to the list of supported database servers.
* [#57](https://github.com/shlinkio/shlink-installer/issues/57) Created new service to handle importing assets from previous versions when updating.

### Changed
* *Nothing*

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*


## [4.0.1] - 2020-01-27
### Added
* [#41](https://github.com/shlinkio/shlink-installer/issues/41) Added gif to readme file which shows how the tool works.

### Changed
* *Nothing*

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* [#55](https://github.com/shlinkio/shlink-installer/issues/55) Fixed GeoLite db file not being imported on updates.


## [4.0.0] - 2020-01-05
### Added
* [#19](https://github.com/shlinkio/shlink-installer/issues/19) Added support to ask for redis servers URIs during installation.

### Changed
* [#38](https://github.com/shlinkio/shlink-installer/issues/38) Configuration generation deeply refactoring, easing including new options.
* [#44](https://github.com/shlinkio/shlink-installer/issues/44) Updated to [coding standard](https://github.com/shlinkio/php-coding-standard) v2.1.0
* [#45](https://github.com/shlinkio/shlink-installer/issues/45) Migrated from Zend Framework components to [Laminas](https://getlaminas.org/).

### Deprecated
* *Nothing*

### Removed
* [#43](https://github.com/shlinkio/shlink-installer/issues/43) Dropped support for PHP 7.2 and 7.3
* [#52](https://github.com/shlinkio/shlink-installer/issues/52) GeoLite2 db is no longer downloaded during installation.

### Fixed
* *Nothing*


## [3.3.0] - 2019-12-29
### Added
* [#37](https://github.com/shlinkio/shlink-installer/issues/37) Added option to ask for visits webhooks.

### Changed
* *Nothing*

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*


## [3.2.0] - 2019-11-30
### Added
* *Nothing*

### Changed
* Updated dependencies and no longer allow build failures on PHP 7.4

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*


## [3.1.0] - 2019-11-10
### Added
* [#30](https://github.com/shlinkio/shlink-installer/issues/30) Added support to ask for the amount of web workers and task workers that should be used when serving the app with swoole.

### Changed
* *Nothing*

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*


## [3.0.0] - 2019-11-02
### Added
* [#31](https://github.com/shlinkio/shlink-installer/issues/31) Added new configuration options for URL redirects.

### Changed
* [#28](https://github.com/shlinkio/shlink-installer/issues/28) Updated coding-standard and infection dependencies.

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*


## [2.1.0] - 2019-10-06
### Added
* Added MariaDB to the list of officially supported databases.

### Changed
* *Nothing*

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*


## [2.0.0] - 2019-09-28
### Added
* [#22](https://github.com/shlinkio/shlink-installer/issues/22) Allowed commands run during installation to be overwritten via configuration.

### Changed
* [#21](https://github.com/shlinkio/shlink-installer/issues/21) Improved question to ask for short domain name, letting know more domains can be added later.

### Deprecated
* *Nothing*

### Removed
* [#23](https://github.com/shlinkio/shlink-installer/issues/23) Removed the option to ask for the language for shlink.

### Fixed
* *Nothing*


## [1.2.1] - 2019-08-05
### Added
* *Nothing*

### Changed
* *Nothing*

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* Fixed shlink config no longer overwriting installer's config, and therefore, not being able to customize anything.


## [1.2.0] - 2019-08-03
### Added
* [#17](https://github.com/shlinkio/shlink-installer/issues/17) Allowed commands run during installation to be overwritten via configuration.

### Changed
* [#10](https://github.com/shlinkio/shlink-installer/issues/10) Updated coding standard to [shlinkio/php-coding-standard](https://github.com/shlinkio/php-coding-standard) v1.1.0.
* [#13](https://github.com/shlinkio/shlink-installer/issues/13) Updated coding standard to [shlinkio/php-coding-standard](https://github.com/shlinkio/php-coding-standard) v1.2.x.

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*


## [1.1.0] - 2019-02-10
### Added
* [#7](https://github.com/shlinkio/shlink-installer/issues/7) Added support to configure the list of questions to ask per plugin.

### Changed
* *Nothing*

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*


## [1.0.0] - 2019-02-06
### Added
* **First release**

### Changed
* *Nothing*

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*
