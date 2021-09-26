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
            Factory\SwooleInstalledFactory::SWOOLE_INSTALLED => Factory\SwooleInstalledFactory::class,

            Command\InstallCommand::class => ConfigAbstractFactory::class,
            Command\UpdateCommand::class => ConfigAbstractFactory::class,
            Command\SetOptionCommand::class => ConfigAbstractFactory::class,
        ],
    ],

    'config_options' => [
        'groups' => [
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
                'Database > Path (SQLite)' => Config\Option\Database\DatabaseSqlitePathConfigOption::class,
                Config\Option\Database\DatabaseMySqlOptionsConfigOption::class,
            ],
            'URL SHORTENER' => [
                'URL shortener > Short domain' => Config\Option\UrlShortener\ShortDomainHostConfigOption::class,
                'URL shortener > Schema' => Config\Option\UrlShortener\ShortDomainSchemaConfigOption::class,
                'URL shortener > Validate URLs' => Config\Option\UrlShortener\ValidateUrlConfigOption::class,
                'URL shortener > Short codes length' => Config\Option\UrlShortener\ShortCodeLengthOption::class,
                'URL shortener > Auto resolve titles'
                    => Config\Option\UrlShortener\AutoResolveTitlesConfigOption::class,
                'URL shortener > Append extra path' => Config\Option\UrlShortener\AppendExtraPathConfigOption::class,
                'Webhooks' => Config\Option\Visit\VisitsWebhooksConfigOption::class,
                'GeoLite2 license key' => Config\Option\UrlShortener\GeoLiteLicenseKeyConfigOption::class,
                'Redirects > Status code (301/302)' => Config\Option\UrlShortener\RedirectStatusCodeConfigOption::class,
                'Redirects > Caching life time' => Config\Option\UrlShortener\RedirectCacheLifeTimeConfigOption::class,
            ],
            'TRACKING' => [
                'Tracking > Orphan visits tracking' => Config\Option\Tracking\OrphanVisitsTrackingConfigOption::class,
                'Tracking > Param to disable tracking' => Config\Option\Tracking\DisableTrackParamConfigOption::class,
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
            ],
            'APPLICATION' => [
                'Delete short URLs > Check threshold' => Config\Option\Visit\CheckVisitsThresholdConfigOption::class,
                'Delete short URLs > Visits threshold amount' => Config\Option\Visit\VisitsThresholdConfigOption::class,
                'Base path' => Config\Option\BasePathConfigOption::class,
                'Swoole > Amount of task workers' => Config\Option\Worker\TaskWorkerNumConfigOption::class,
                'Swoole > Amount of web workers' => Config\Option\Worker\WebWorkerNumConfigOption::class,
            ],
            'INTEGRATIONS' => [
                'Redis servers' => Config\Option\RedisServersConfigOption::class,
                Config\Option\Mercure\EnableMercureConfigOption::class,
                'Mercure > Public URL' => Config\Option\Mercure\MercurePublicUrlConfigOption::class,
                'Mercure > Internal URL' => Config\Option\Mercure\MercureInternalUrlConfigOption::class,
                'Mercure > JWT Secret' => Config\Option\Mercure\MercureJwtSecretConfigOption::class,
            ],
        ],

        'factories' => [
            Config\Option\BasePathConfigOption::class => InvokableFactory::class,
            Config\Option\Visit\CheckVisitsThresholdConfigOption::class => InvokableFactory::class,
            Config\Option\Visit\VisitsThresholdConfigOption::class => InvokableFactory::class,
            Config\Option\Database\DatabaseDriverConfigOption::class => InvokableFactory::class,
            Config\Option\Database\DatabaseNameConfigOption::class => InvokableFactory::class,
            Config\Option\Database\DatabaseHostConfigOption::class => InvokableFactory::class,
            Config\Option\Database\DatabasePortConfigOption::class => InvokableFactory::class,
            Config\Option\Database\DatabaseUserConfigOption::class => InvokableFactory::class,
            Config\Option\Database\DatabasePasswordConfigOption::class => InvokableFactory::class,
            Config\Option\Database\DatabaseUnixSocketConfigOption::class => InvokableFactory::class,
            Config\Option\Database\DatabaseSqlitePathConfigOption::class => InvokableFactory::class,
            Config\Option\Database\DatabaseMySqlOptionsConfigOption::class => InvokableFactory::class,
            Config\Option\Redirect\BaseUrlRedirectConfigOption::class => InvokableFactory::class,
            Config\Option\Redirect\InvalidShortUrlRedirectConfigOption::class => InvokableFactory::class,
            Config\Option\Redirect\Regular404RedirectConfigOption::class => InvokableFactory::class,
            Config\Option\UrlShortener\ShortDomainHostConfigOption::class => InvokableFactory::class,
            Config\Option\UrlShortener\ShortDomainSchemaConfigOption::class => InvokableFactory::class,
            Config\Option\UrlShortener\ValidateUrlConfigOption::class => InvokableFactory::class,
            Config\Option\UrlShortener\AutoResolveTitlesConfigOption::class => InvokableFactory::class,
            Config\Option\UrlShortener\AppendExtraPathConfigOption::class => InvokableFactory::class,
            Config\Option\RedisServersConfigOption::class => InvokableFactory::class,
            Config\Option\Visit\VisitsWebhooksConfigOption::class => ConfigAbstractFactory::class,
            Config\Option\Worker\TaskWorkerNumConfigOption::class => ConfigAbstractFactory::class,
            Config\Option\Worker\WebWorkerNumConfigOption::class => ConfigAbstractFactory::class,
            Config\Option\UrlShortener\ShortCodeLengthOption::class => InvokableFactory::class,
            Config\Option\Mercure\EnableMercureConfigOption::class => ConfigAbstractFactory::class,
            Config\Option\Mercure\MercurePublicUrlConfigOption::class => ConfigAbstractFactory::class,
            Config\Option\Mercure\MercureInternalUrlConfigOption::class => ConfigAbstractFactory::class,
            Config\Option\Mercure\MercureJwtSecretConfigOption::class => ConfigAbstractFactory::class,
            Config\Option\UrlShortener\GeoLiteLicenseKeyConfigOption::class => InvokableFactory::class,
            Config\Option\Tracking\OrphanVisitsTrackingConfigOption::class => InvokableFactory::class,
            Config\Option\Tracking\DisableTrackParamConfigOption::class => InvokableFactory::class,
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
        ],
    ],

    ConfigAbstractFactory::class => [
        Config\Option\Visit\VisitsWebhooksConfigOption::class => [Factory\SwooleInstalledFactory::SWOOLE_INSTALLED],
        Config\Option\Worker\TaskWorkerNumConfigOption::class => [Factory\SwooleInstalledFactory::SWOOLE_INSTALLED],
        Config\Option\Worker\WebWorkerNumConfigOption::class => [Factory\SwooleInstalledFactory::SWOOLE_INSTALLED],
        Config\Option\Mercure\EnableMercureConfigOption::class => [Factory\SwooleInstalledFactory::SWOOLE_INSTALLED],
        Config\Option\Mercure\MercurePublicUrlConfigOption::class => [Factory\SwooleInstalledFactory::SWOOLE_INSTALLED],
        Config\Option\Mercure\MercureInternalUrlConfigOption::class => [
            Factory\SwooleInstalledFactory::SWOOLE_INSTALLED,
        ],
        Config\Option\Mercure\MercureJwtSecretConfigOption::class => [Factory\SwooleInstalledFactory::SWOOLE_INSTALLED],

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
            Service\InstallationCommandsRunner::class,
        ],
        Command\UpdateCommand::class => [
            PhpArrayConfigWriter::class,
            Service\ShlinkAssetsHandler::class,
            Config\ConfigGenerator::class,
            Service\InstallationCommandsRunner::class,
        ],
        Command\SetOptionCommand::class => [
            PhpArrayConfigWriter::class,
            Service\ShlinkAssetsHandler::class,
            Config\ConfigOptionsManager::class,
            Filesystem::class,
            'config.config_options.groups',
            'config.installer.enabled_options',
        ],
    ],

    'installer' => [
        'commands' => [
            Command\InstallCommand::NAME => Command\InstallCommand::class,
            Command\UpdateCommand::NAME => Command\UpdateCommand::class,
            Command\SetOptionCommand::NAME => Command\SetOptionCommand::class,
        ],

        'enabled_options' => null,

        'installation_commands' => [
            InstallationCommand::DB_CREATE_SCHEMA => [
                'command' => 'vendor/doctrine/orm/bin/doctrine.php orm:schema-tool:create',
                'initMessage' => 'Initializing database...',
                'errorMessage' => 'Error generating database.',
                'failOnError' => true,
            ],
            InstallationCommand::DB_MIGRATE => [
                'command' => 'vendor/doctrine/migrations/bin/doctrine-migrations.php migrations:migrate',
                'initMessage' => 'Updating database...',
                'errorMessage' => 'Error updating database.',
                'failOnError' => true,
            ],
            InstallationCommand::ORM_PROXIES => [
                'command' => 'vendor/doctrine/orm/bin/doctrine.php orm:generate-proxies',
                'initMessage' => 'Generating proxies...',
                'errorMessage' => 'Error generating proxies.',
                'failOnError' => true,
            ],
            InstallationCommand::ORM_CLEAR_CACHE => [
                'command' => 'vendor/doctrine/orm/bin/doctrine.php orm:clear-cache:metadata',
                'initMessage' => 'Clearing entities cache...',
                'errorMessage' => 'Error clearing entities cache.',
                'failOnError' => false,
            ],
            InstallationCommand::GEOLITE_DOWNLOAD_DB => [
                'command' => null, // Disabled by default, to avoid dependency on consumer (Shlink)
                'initMessage' => 'Downloading GeoLite2 db file...',
                'errorMessage' => 'Error downloading GeoLite2 db.',
                'failOnError' => false,
            ],
        ],
    ],

];
