<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer;

use Laminas\Config\Writer\PhpArray as PhpArrayConfigWriter;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Shlinkio\Shlink\Installer\Util\InstallationCommand;
use Symfony\Component\Console;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\PhpExecutableFinder;

return [

    'dependencies' => [
        'factories' => [
            Console\Application::class => Factory\ApplicationFactory::class,
            Filesystem::class => InvokableFactory::class,
            PhpExecutableFinder::class => InvokableFactory::class,
            PhpArrayConfigWriter::class => InvokableFactory::class,
            Console\Helper\ProcessHelper::class => Factory\ProcessHelperFactory::class,

            Service\InstallationCommandsRunner::class => ConfigAbstractFactory::class,
            Service\ShlinkAssetsHandler::class => ConfigAbstractFactory::class,
            Config\ConfigGenerator::class => ConfigAbstractFactory::class,
            Config\ConfigOptionsManager::class => Config\ConfigOptionsManagerFactory::class,

            Command\InstallCommand::class => ConfigAbstractFactory::class,
            Command\UpdateCommand::class => ConfigAbstractFactory::class,
            Command\SetOptionCommand::class => ConfigAbstractFactory::class,
            Command\InitCommand::class => ConfigAbstractFactory::class,
        ],
    ],

    'config_options' => [
        'groups' => [
            // Options with no explicit key are ignored by set-option command, but asked normally by install and update
            // commands
            'SERVER' => [
                Config\Option\Server\RuntimeConfigOption::class,
                'Memory limit' => Config\Option\Server\MemoryLimitConfigOption::class,
            ],
            'DATABASE' => [
                'Database > Driver' => Config\Option\Database\DatabaseDriverConfigOption::class,
                'Database > Name' => Config\Option\Database\DatabaseNameConfigOption::class,
                'Database > Host (or unix socket for PostgreSQL)'
                    => Config\Option\Database\DatabaseHostConfigOption::class,
                'Database > Port' => Config\Option\Database\DatabasePortConfigOption::class,
                'Database > User' => Config\Option\Database\DatabaseUserConfigOption::class,
                'Database > Password' => Config\Option\Database\DatabasePasswordConfigOption::class,
                'Database > Unix socket (Mysql/MariaDB)'
                    => Config\Option\Database\DatabaseUnixSocketConfigOption::class,
            ],
            'URL SHORTENER' => [
                'URL shortener > Short domain' => Config\Option\UrlShortener\ShortDomainHostConfigOption::class,
                'URL shortener > Schema' => Config\Option\UrlShortener\ShortDomainSchemaConfigOption::class,
                'URL shortener > Short codes length' => Config\Option\UrlShortener\ShortCodeLengthOption::class,
                'URL shortener > Auto resolve titles'
                    => Config\Option\UrlShortener\AutoResolveTitlesConfigOption::class,
                'URL shortener > Append extra path' => Config\Option\UrlShortener\AppendExtraPathConfigOption::class,
                'URL shortener > Multi-segment slugs'
                    => Config\Option\UrlShortener\EnableMultiSegmentSlugsConfigOption::class,
                'URL shortener > Trailing slashes' => Config\Option\UrlShortener\EnableTrailingSlashConfigOption::class,
                'URL shortener > Mode' => Config\Option\UrlShortener\ShortUrlModeConfigOption::class,
                'GeoLite2 license key' => Config\Option\UrlShortener\GeoLiteLicenseKeyConfigOption::class,
                'Redirects > Status code (301/302)' => Config\Option\UrlShortener\RedirectStatusCodeConfigOption::class,
                'Redirects > Caching life time' => Config\Option\UrlShortener\RedirectCacheLifeTimeConfigOption::class,
            ],
            'TRACKING' => [
                'Tracking > Orphan visits tracking' => Config\Option\Tracking\OrphanVisitsTrackingConfigOption::class,
                'Tracking > Param to disable tracking' => Config\Option\Tracking\DisableTrackParamConfigOption::class,
                'Tracking > Disabled IP addresses' => Config\Option\Tracking\DisableTrackingFromConfigOption::class,
                'Tracking > Disable tracking' => Config\Option\Tracking\DisableTrackingConfigOption::class,
                'Tracking > Disable IP address tracking' => Config\Option\Tracking\DisableIpTrackingConfigOption::class,
                'Tracking > IP Anonymization' => Config\Option\Tracking\IpAnonymizationConfigOption::class,
                'Tracking > Disable user agent tracking' => Config\Option\Tracking\DisableUaTrackingConfigOption::class,
                'Tracking > Disable referrer tracking'
                    => Config\Option\Tracking\DisableReferrerTrackingConfigOption::class,
            ],
            'REDIRECTS' => [
                'Redirects > Base URL' => Config\Option\Redirect\BaseUrlRedirectConfigOption::class,
                'Redirects > Invalid short URL' => Config\Option\Redirect\InvalidShortUrlRedirectConfigOption::class,
                'Redirects > Regular 404' => Config\Option\Redirect\Regular404RedirectConfigOption::class,
            ],
            'QR CODES' => [
                'QR codes > Default size' => Config\Option\QrCode\DefaultSizeConfigOption::class,
                'QR codes > Default margin' => Config\Option\QrCode\DefaultMarginConfigOption::class,
                'QR codes > Default format' => Config\Option\QrCode\DefaultFormatConfigOption::class,
                'QR codes > Default error correction' => Config\Option\QrCode\DefaultErrorCorrectionConfigOption::class,
                'QR codes > Default round block size' => Config\Option\QrCode\DefaultRoundBlockSizeConfigOption::class,
                'QR codes > Default color' => Config\Option\QrCode\DefaultColorConfigOption::class,
                'QR codes > Default background color' => Config\Option\QrCode\DefaultBgColorConfigOption::class,
                'QR codes > Default logo URL' => Config\Option\QrCode\DefaultLogoUrlConfigOption::class,
                'QR codes > Enabled for disabled short URLs'
                    => Config\Option\QrCode\EnabledForDisabledShortUrlsConfigOption::class,
            ],
            'APPLICATION' => [
                'Delete short URLs > Visits threshold' => Config\Option\Visit\VisitsThresholdConfigOption::class,
                'Base path' => Config\Option\BasePathConfigOption::class,
                'Timezone' => Config\Option\TimezoneConfigOption::class,
                'Cache > namespace' => Config\Option\Cache\CacheNamespaceConfigOption::class,
            ],
            'INTEGRATIONS' => [
                'Redis > servers' => Config\Option\Redis\RedisServersConfigOption::class,
                'Redis > sentinels service' => Config\Option\Redis\RedisSentinelServiceConfigOption::class,
                'Redis > Pub/sub enabled' => Config\Option\Redis\RedisPubSubConfigOption::class,
                Config\Option\Mercure\EnableMercureConfigOption::class,
                'Mercure > Public URL' => Config\Option\Mercure\MercurePublicUrlConfigOption::class,
                'Mercure > Internal URL' => Config\Option\Mercure\MercureInternalUrlConfigOption::class,
                'Mercure > JWT Secret' => Config\Option\Mercure\MercureJwtSecretConfigOption::class,
                'RabbitMQ > Enable' => Config\Option\RabbitMq\RabbitMqEnabledConfigOption::class,
                'RabbitMQ > Host' => Config\Option\RabbitMq\RabbitMqHostConfigOption::class,
                'RabbitMQ > SSL' => Config\Option\RabbitMq\RabbitMqUseSslConfigOption::class,
                'RabbitMQ > Port' => Config\Option\RabbitMq\RabbitMqPortConfigOption::class,
                'RabbitMQ > User' => Config\Option\RabbitMq\RabbitMqUserConfigOption::class,
                'RabbitMQ > Password' => Config\Option\RabbitMq\RabbitMqPasswordConfigOption::class,
                'RabbitMQ > VHost' => Config\Option\RabbitMq\RabbitMqVhostConfigOption::class,
                'Matomo > Enable' => Config\Option\Matomo\MatomoEnabledConfigOption::class,
                'Matomo > Server URL' => Config\Option\Matomo\MatomoBaseUrlConfigOption::class,
                'Matomo > Site ID' => Config\Option\Matomo\MatomoSiteIdConfigOption::class,
                'Matomo > API token' => Config\Option\Matomo\MatomoApiTokenConfigOption::class,
            ],
        ],

        'factories' => [
            Config\Option\Server\RuntimeConfigOption::class => InvokableFactory::class,
            Config\Option\Server\MemoryLimitConfigOption::class => InvokableFactory::class,
            Config\Option\BasePathConfigOption::class => InvokableFactory::class,
            Config\Option\TimezoneConfigOption::class => InvokableFactory::class,
            Config\Option\Cache\CacheNamespaceConfigOption::class => InvokableFactory::class,
            Config\Option\Visit\VisitsThresholdConfigOption::class => InvokableFactory::class,
            Config\Option\Database\DatabaseDriverConfigOption::class => InvokableFactory::class,
            Config\Option\Database\DatabaseNameConfigOption::class => InvokableFactory::class,
            Config\Option\Database\DatabaseHostConfigOption::class => InvokableFactory::class,
            Config\Option\Database\DatabasePortConfigOption::class => InvokableFactory::class,
            Config\Option\Database\DatabaseUserConfigOption::class => InvokableFactory::class,
            Config\Option\Database\DatabasePasswordConfigOption::class => InvokableFactory::class,
            Config\Option\Database\DatabaseUnixSocketConfigOption::class => InvokableFactory::class,
            Config\Option\Redirect\BaseUrlRedirectConfigOption::class => InvokableFactory::class,
            Config\Option\Redirect\InvalidShortUrlRedirectConfigOption::class => InvokableFactory::class,
            Config\Option\Redirect\Regular404RedirectConfigOption::class => InvokableFactory::class,
            Config\Option\UrlShortener\ShortDomainHostConfigOption::class => InvokableFactory::class,
            Config\Option\UrlShortener\ShortDomainSchemaConfigOption::class => InvokableFactory::class,
            Config\Option\UrlShortener\AutoResolveTitlesConfigOption::class => InvokableFactory::class,
            Config\Option\UrlShortener\AppendExtraPathConfigOption::class => InvokableFactory::class,
            Config\Option\UrlShortener\EnableMultiSegmentSlugsConfigOption::class => InvokableFactory::class,
            Config\Option\UrlShortener\EnableTrailingSlashConfigOption::class => InvokableFactory::class,
            Config\Option\UrlShortener\ShortUrlModeConfigOption::class => InvokableFactory::class,
            Config\Option\Redis\RedisServersConfigOption::class => InvokableFactory::class,
            Config\Option\Redis\RedisSentinelServiceConfigOption::class => InvokableFactory::class,
            Config\Option\Redis\RedisPubSubConfigOption::class => InvokableFactory::class,
            Config\Option\UrlShortener\ShortCodeLengthOption::class => InvokableFactory::class,
            Config\Option\Mercure\EnableMercureConfigOption::class => InvokableFactory::class,
            Config\Option\Mercure\MercurePublicUrlConfigOption::class => InvokableFactory::class,
            Config\Option\Mercure\MercureInternalUrlConfigOption::class => InvokableFactory::class,
            Config\Option\Mercure\MercureJwtSecretConfigOption::class => InvokableFactory::class,
            Config\Option\RabbitMq\RabbitMqEnabledConfigOption::class => InvokableFactory::class,
            Config\Option\RabbitMq\RabbitMqHostConfigOption::class => InvokableFactory::class,
            Config\Option\RabbitMq\RabbitMqUseSslConfigOption::class => InvokableFactory::class,
            Config\Option\RabbitMq\RabbitMqPortConfigOption::class => InvokableFactory::class,
            Config\Option\RabbitMq\RabbitMqUserConfigOption::class => InvokableFactory::class,
            Config\Option\RabbitMq\RabbitMqPasswordConfigOption::class => InvokableFactory::class,
            Config\Option\RabbitMq\RabbitMqVhostConfigOption::class => InvokableFactory::class,
            Config\Option\Matomo\MatomoEnabledConfigOption::class => InvokableFactory::class,
            Config\Option\Matomo\MatomoBaseUrlConfigOption::class => InvokableFactory::class,
            Config\Option\Matomo\MatomoSiteIdConfigOption::class => InvokableFactory::class,
            Config\Option\Matomo\MatomoApiTokenConfigOption::class => InvokableFactory::class,
            Config\Option\UrlShortener\GeoLiteLicenseKeyConfigOption::class => InvokableFactory::class,
            Config\Option\Tracking\OrphanVisitsTrackingConfigOption::class => InvokableFactory::class,
            Config\Option\Tracking\DisableTrackParamConfigOption::class => InvokableFactory::class,
            Config\Option\Tracking\DisableTrackingFromConfigOption::class => InvokableFactory::class,
            Config\Option\Tracking\DisableTrackingConfigOption::class => InvokableFactory::class,
            Config\Option\Tracking\DisableIpTrackingConfigOption::class => InvokableFactory::class,
            Config\Option\Tracking\IpAnonymizationConfigOption::class => InvokableFactory::class,
            Config\Option\Tracking\DisableReferrerTrackingConfigOption::class => InvokableFactory::class,
            Config\Option\Tracking\DisableUaTrackingConfigOption::class => InvokableFactory::class,
            Config\Option\UrlShortener\RedirectStatusCodeConfigOption::class => InvokableFactory::class,
            Config\Option\UrlShortener\RedirectCacheLifeTimeConfigOption::class => InvokableFactory::class,
            Config\Option\QrCode\DefaultSizeConfigOption::class => InvokableFactory::class,
            Config\Option\QrCode\DefaultMarginConfigOption::class => InvokableFactory::class,
            Config\Option\QrCode\DefaultFormatConfigOption::class => InvokableFactory::class,
            Config\Option\QrCode\DefaultErrorCorrectionConfigOption::class => InvokableFactory::class,
            Config\Option\QrCode\DefaultRoundBlockSizeConfigOption::class => InvokableFactory::class,
            Config\Option\QrCode\DefaultColorConfigOption::class => InvokableFactory::class,
            Config\Option\QrCode\DefaultBgColorConfigOption::class => InvokableFactory::class,
            Config\Option\QrCode\DefaultLogoUrlConfigOption::class => InvokableFactory::class,
            Config\Option\QrCode\EnabledForDisabledShortUrlsConfigOption::class => InvokableFactory::class,
        ],
    ],

    ConfigAbstractFactory::class => [
        Config\ConfigGenerator::class => [
            Config\ConfigOptionsManager::class,
            'config.config_options.groups',
            'config.installer.enabled_options',
        ],
        Service\ShlinkAssetsHandler::class => [Filesystem::class],
        Service\InstallationCommandsRunner::class => [
            Console\Helper\ProcessHelper::class,
            PhpExecutableFinder::class,
            'config.installer.installation_commands',
        ],

        Command\InstallCommand::class => [
            PhpArrayConfigWriter::class,
            Service\ShlinkAssetsHandler::class,
            Config\ConfigGenerator::class,
        ],
        Command\UpdateCommand::class => [
            PhpArrayConfigWriter::class,
            Service\ShlinkAssetsHandler::class,
            Config\ConfigGenerator::class,
        ],
        Command\SetOptionCommand::class => [
            PhpArrayConfigWriter::class,
            Service\ShlinkAssetsHandler::class,
            Config\ConfigOptionsManager::class,
            Filesystem::class,
            'config.config_options.groups',
            'config.installer.enabled_options',
        ],
        Command\InitCommand::class => [Service\InstallationCommandsRunner::class],
    ],

    'installer' => [
        'commands' => [
            Command\InstallCommand::NAME => Command\InstallCommand::class,
            Command\UpdateCommand::NAME => Command\UpdateCommand::class,
            Command\SetOptionCommand::NAME => Command\SetOptionCommand::class,
            Command\InitCommand::NAME => Command\InitCommand::class,
        ],

        'enabled_options' => null,

        'installation_commands' => [
            InstallationCommand::DB_CREATE_SCHEMA->value => [
                'command' => 'vendor/doctrine/orm/bin/doctrine.php orm:schema-tool:create',
                'initMessage' => 'Initializing database if needed...',
                'errorMessage' => 'Error generating database.',
                'failOnError' => true,
                'printOutput' => false,
                'timeout' => 600, // 10 minutes
            ],
            InstallationCommand::DB_MIGRATE->value => [
                'command' => 'vendor/doctrine/migrations/bin/doctrine-migrations.php migrations:migrate',
                'initMessage' => 'Updating database...',
                'errorMessage' => 'Error updating database.',
                'failOnError' => true,
                'printOutput' => false,
                'timeout' => 600, // 10 minutes
            ],
            InstallationCommand::ORM_PROXIES->value => [
                'command' => 'vendor/doctrine/orm/bin/doctrine.php orm:generate-proxies',
                'initMessage' => 'Generating proxies...',
                'errorMessage' => 'Error generating proxies.',
                'failOnError' => true,
                'printOutput' => false,
            ],
            InstallationCommand::ORM_CLEAR_CACHE->value => [
                'command' => 'vendor/doctrine/orm/bin/doctrine.php orm:clear-cache:metadata',
                'initMessage' => 'Clearing entities cache...',
                'errorMessage' => 'Error clearing entities cache.',
                'failOnError' => false,
                'printOutput' => false,
            ],
            InstallationCommand::GEOLITE_DOWNLOAD_DB->value => [
                'command' => null, // Disabled by default, to avoid dependency on consumer (Shlink)
                'initMessage' => 'Downloading GeoLite2 db file...',
                'errorMessage' => 'Error downloading GeoLite2 db.',
                'failOnError' => false,
                'printOutput' => false,
            ],
            InstallationCommand::API_KEY_GENERATE->value => [
                'command' => null, // Disabled by default, to avoid dependency on consumer (Shlink)
                'initMessage' => 'Generating first API key...',
                'errorMessage' => 'Error generating first API key.',
                'failOnError' => false,
                'printOutput' => true,
            ],
            InstallationCommand::API_KEY_CREATE->value => [
                'command' => null, // Disabled by default, to avoid dependency on consumer (Shlink)
                'initMessage' => 'Creating first API key...',
                'errorMessage' => 'Error creating first API key.',
                'failOnError' => false,
                'printOutput' => true,
            ],
            InstallationCommand::ROAD_RUNNER_BINARY_DOWNLOAD->value => [
                'command' => 'vendor/bin/rr get --no-interaction --no-config --location bin/',
                'initMessage' => 'Downloading RoadRunner binary...',
                'errorMessage' => 'Error downloading RoadRunner binary.',
                'failOnError' => false,
                'printOutput' => false,
            ],
        ],
    ],

];
