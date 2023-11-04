# CHANGELOG

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com), and this project adheres to [Semantic Versioning](https://semver.org).

## [Unreleased]
### Added
* Add `CacheNamespaceConfigOption` to customize the cache namespace.
* Add support for PHP 8.3

### Changed
* *Nothing*

### Deprecated
* *Nothing*

### Removed
* Drop support for PHP 8.1

### Fixed
* *Nothing*


## [8.5.0] - 2023-09-22
### Added
* Improve `init` command's `--initial-api-key` flag, so that it can receive an optional value which will be used as the initial API key.

### Changed
* [#193](https://github.com/shlinkio/shlink-installer/issues/193) Display improved verbosity hint for installation commands based on `interactive` flag, suggesting `-vvv` for interactive executions, and `SHELL_VERBOSITY=3` for non-interactive ones.
* Display warning next to SQLite when selecting database, informing it is not supported for production setups.

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*


## [8.4.2] - 2023-06-15
### Added
* *Nothing*

### Changed
* *Nothing*

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* Make sure installation commands are run with the right timeout


## [8.4.1] - 2023-06-08
### Added
* *Nothing*

### Changed
* *Nothing*

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* Fix silent error when trying to download rr binary


## [8.4.0] - 2023-05-23
### Added
* [#183](https://github.com/shlinkio/shlink-installer/issues/183) Create new `init` command that can be used to set up and initialize the environment for Shlink.

    This command makes sure the database is created, caches are cleared, etc., and can be used by those who wish to automate Shlink installations with env vars instead of the interactive `install`/`update`.

    The existing `install` and `update` commands use this one internally, and it is also suitable for the docker image entry point.

* [#184](https://github.com/shlinkio/shlink-installer/issues/184) During updates, the installer can now detect if the RoadRunner binary exists in the "old" installation folder, in which case it downloads a new instance as part of the process.

### Changed
* Changed `loosely` by `loose` for the short URL mode, ensuring a migration for those who previously set `loosely`.
* Updated to PHPUnit 10 and migrated config to PHPUnit 10.1 format.

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*


## [8.3.0] - 2023-01-28
### Added
* [#174](https://github.com/shlinkio/shlink-installer/issues/174) Added support for redirect status codes 308 and 307.
* Added support for short URL mode option.

### Changed
* Migrated infection config to json5.
* Migrated test doubles from prophecy to PHPUnit mocks.
* Replaced references to `doma.in` by `s.test`.

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*


## [8.2.0] - 2022-09-18
### Added
* Added config option to enable/disable trailing slashes support.
* Added new script to make sure first API key is generated after successfully installing Shlink.

### Changed
* *Nothing*

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*


## [8.1.0] - 2022-08-08
### Added
* *Nothing*

### Changed
* Updated to shlink-config 2.0.

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*


## [8.0.0] - 2022-08-04
### Added
* [#162](https://github.com/shlinkio/shlink-installer/issues/162) Added support for the redis pub/sub config option.
* [#166](https://github.com/shlinkio/shlink-installer/issues/166) Added support for the multi-segment slugs config option.

### Changed
* *Nothing*

### Deprecated
* *Nothing*

### Removed
* [#161](https://github.com/shlinkio/shlink-installer/issues/161) Dropped support for PHP 8.0
* [#151](https://github.com/shlinkio/shlink-installer/issues/151) Removed compatibility with config path approach. Only env vars are supported now.

### Fixed
* *Nothing*


## [7.1.0] - 2022-04-23
### Added
* [#157](https://github.com/shlinkio/shlink-installer/issues/157) Added support for the timezone config option.

### Changed
* *Nothing*

### Deprecated
* Deprecated webhook-related config options.

### Removed
* *Nothing*

### Fixed
* [#155](https://github.com/shlinkio/shlink-installer/issues/155) Fixed router config cache not getting deleted when editing config options.


## [7.0.2] - 2022-02-19
### Added
* *Nothing*

### Changed
* *Nothing*

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* Fixed delete threshold being always saved as 1.


## [7.0.1] - 2022-02-09
### Added
* *Nothing*

### Changed
* *Nothing*

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* Fixed non-sqlite questions being asked when importing a pre-7.0 config using SQLite.


## [7.0.0] - 2022-01-27
### Added
* [#143](https://github.com/shlinkio/shlink-installer/issues/143) Reworked how config options are "persisted", switching from regular config to an env var map.

### Changed
* Dropped support for Symfony 5.
* Updated to infection 0.26, enabling HTML reports.
* Added explicitly enabled composer plugins to composer.json.

### Deprecated
* *Nothing*

### Removed
* Removed everything that was deprecated

### Fixed
* *Nothing*


## [6.3.0] - 2021-12-12
### Added
* [#140](https://github.com/shlinkio/shlink-installer/issues/140) Added support for RabbitMQ options.
* Added support for PHP 8.1.
* Added support for Symfony 6.0.
* Added support for openswoole.
* Added "round block size" config option for QR codes.

### Changed
* Updated to phpstan 1.0

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*


## [6.2.1] - 2021-10-23
### Added
* *Nothing*

### Changed
* Moved ci workflow to external repo and reused

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* [#132](https://github.com/shlinkio/shlink-installer/issues/132) Ensured a minimum amount of task workers.


## [6.2.0] - 2021-10-10
### Added
* [#122](https://github.com/shlinkio/shlink-installer/issues/122) Added support for QR code config options.
* [#124](https://github.com/shlinkio/shlink-installer/issues/124) Added support for redis sentinels in redis config.
* [#126](https://github.com/shlinkio/shlink-installer/issues/126) Added support to send orphan visits to webhooks, if any.
* [#128](https://github.com/shlinkio/shlink-installer/issues/128) Added support for IP-based tracking.

### Changed
* *Nothing*

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*


## [6.1.0] - 2021-08-04
### Added
* [#115](https://github.com/shlinkio/shlink-installer/issues/115) Added support for `append_extra_path` config option.

### Changed
* [#117](https://github.com/shlinkio/shlink-installer/issues/117) Added experimental builds under PHP 8.1
* [#120](https://github.com/shlinkio/shlink-installer/issues/120) Increased required PHPStan level to 8.

### Deprecated
* *Nothing*

### Removed
* [#118](https://github.com/shlinkio/shlink-installer/issues/118) Dropped support for PHP 7.4

### Fixed
* *Nothing*


## [6.0.0] - 2021-05-22
### Added
* [#86](https://github.com/shlinkio/shlink-installer/issues/86) Added new optional installation command to download GeoLite2 db file.
* [#109](https://github.com/shlinkio/shlink-installer/issues/109) Added ability to define deprecated config paths for options, that are transparently migrated to the new one during update.
* [#108](https://github.com/shlinkio/shlink-installer/issues/108) Added new tracking options.

### Changed
* [#106](https://github.com/shlinkio/shlink-installer/issues/106) Increased required mutation score to 90%.
* [#112](https://github.com/shlinkio/shlink-installer/issues/112) Ensured IP anonymization option is only asked if tracking or IP tracking have not been disabled.

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*


## [5.4.0] - 2021-02-13
### Added
* [#101](https://github.com/shlinkio/shlink-installer/issues/101) Added new "auto generate titles" option.
* [#103](https://github.com/shlinkio/shlink-installer/issues/103) Added "track orphan visits" option.

### Changed
* Migrated build to Github Actions.

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*


## [5.3.0] - 2020-12-11
### Added
* [#51](https://github.com/shlinkio/shlink-installer/issues/51) Created new command that allows updating the value of any configuration option.

### Changed
* [#96](https://github.com/shlinkio/shlink-installer/issues/96) Updated required MSI to 85%.

### Deprecated
* *Nothing*

### Removed
* *Nothing*

### Fixed
* *Nothing*


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
