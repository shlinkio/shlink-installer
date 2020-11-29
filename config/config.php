<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer;

use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Symfony\Component\Console;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\PhpExecutableFinder;

return [

    'dependencies' => [
        'factories' => [
            Console\Application::class => Factory\InstallApplicationFactory::class,
            Filesystem::class => InvokableFactory::class,
            PhpExecutableFinder::class => InvokableFactory::class,
            Console\Helper\ProcessHelper::class => Factory\ProcessHelperFactory::class,

            Service\InstallationCommandsRunner::class => ConfigAbstractFactory::class,
            Service\ShlinkAssetsHandler::class => ConfigAbstractFactory::class,
            Config\ConfigGenerator::class => Config\ConfigGeneratorFactory::class,
            Factory\SwooleInstalledFactory::SWOOLE_INSTALLED => Factory\SwooleInstalledFactory::class,
        ],
    ],

    'config_options' => [
        'groups' => [
            'DATABASE' => [
                Config\Option\Database\DatabaseDriverConfigOption::class,
                Config\Option\Database\DatabaseNameConfigOption::class,
                Config\Option\Database\DatabaseHostConfigOption::class,
                Config\Option\Database\DatabasePortConfigOption::class,
                Config\Option\Database\DatabaseUserConfigOption::class,
                Config\Option\Database\DatabasePasswordConfigOption::class,
                Config\Option\Database\DatabaseUnixSocketConfigOption::class,
                Config\Option\Database\DatabaseSqlitePathConfigOption::class,
                Config\Option\Database\DatabaseMySqlOptionsConfigOption::class,
            ],
            'URL SHORTENER' => [
                Config\Option\UrlShortener\ShortDomainHostConfigOption::class,
                Config\Option\UrlShortener\ShortDomainSchemaConfigOption::class,
                Config\Option\UrlShortener\ValidateUrlConfigOption::class,
                Config\Option\UrlShortener\ShortCodeLengthOption::class,
                Config\Option\Visit\VisitsWebhooksConfigOption::class,
                Config\Option\UrlShortener\GeoLiteLicenseKeyConfigOption::class,
                Config\Option\UrlShortener\IpAnonymizationConfigOption::class,
                Config\Option\UrlShortener\RedirectStatusCodeConfigOption::class,
                Config\Option\UrlShortener\RedirectCacheLifeTimeConfigOption::class,
            ],
            'REDIRECTS' => [
                Config\Option\Redirect\BaseUrlRedirectConfigOption::class,
                Config\Option\Redirect\InvalidShortUrlRedirectConfigOption::class,
                Config\Option\Redirect\Regular404RedirectConfigOption::class,
            ],
            'APPLICATION' => [
                Config\Option\DisableTrackParamConfigOption::class,
                Config\Option\Visit\CheckVisitsThresholdConfigOption::class,
                Config\Option\Visit\VisitsThresholdConfigOption::class,
                Config\Option\BasePathConfigOption::class,
                Config\Option\Worker\TaskWorkerNumConfigOption::class,
                Config\Option\Worker\WebWorkerNumConfigOption::class,
            ],
            'INTEGRATIONS' => [
                Config\Option\RedisServersConfigOption::class,
                Config\Option\Mercure\EnableMercureConfigOption::class,
                Config\Option\Mercure\MercurePublicUrlConfigOption::class,
                Config\Option\Mercure\MercureInternalUrlConfigOption::class,
                Config\Option\Mercure\MercureJwtSecretConfigOption::class,
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
            Config\Option\DisableTrackParamConfigOption::class => InvokableFactory::class,
            Config\Option\Redirect\BaseUrlRedirectConfigOption::class => InvokableFactory::class,
            Config\Option\Redirect\InvalidShortUrlRedirectConfigOption::class => InvokableFactory::class,
            Config\Option\Redirect\Regular404RedirectConfigOption::class => InvokableFactory::class,
            Config\Option\UrlShortener\ShortDomainHostConfigOption::class => InvokableFactory::class,
            Config\Option\UrlShortener\ShortDomainSchemaConfigOption::class => InvokableFactory::class,
            Config\Option\UrlShortener\ValidateUrlConfigOption::class => InvokableFactory::class,
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
            Config\Option\UrlShortener\IpAnonymizationConfigOption::class => InvokableFactory::class,
            Config\Option\UrlShortener\RedirectStatusCodeConfigOption::class => InvokableFactory::class,
            Config\Option\UrlShortener\RedirectCacheLifeTimeConfigOption::class => InvokableFactory::class,
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
        Service\ShlinkAssetsHandler::class => [Filesystem::class],
        Service\InstallationCommandsRunner::class => [
            Console\Helper\ProcessHelper::class,
            PhpExecutableFinder::class,
            'config.installer.installation_commands',
        ],
    ],

    'installer' => [
        'installation_commands' => [
            'db_create_schema' => [
                'command' => 'vendor/doctrine/orm/bin/doctrine.php orm:schema-tool:create',
                'initMessage' => 'Initializing database...',
                'errorMessage' => 'Error generating database.',
                'failOnError' => true,
            ],
            'db_migrate' => [
                'command' => 'vendor/doctrine/migrations/bin/doctrine-migrations.php migrations:migrate',
                'initMessage' => 'Updating database...',
                'errorMessage' => 'Error updating database.',
                'failOnError' => true,
            ],
            'orm_proxies' => [
                'command' => 'vendor/doctrine/orm/bin/doctrine.php orm:generate-proxies',
                'initMessage' => 'Generating proxies...',
                'errorMessage' => 'Error generating proxies.',
                'failOnError' => true,
            ],
            'orm_clear_cache' => [
                'command' => 'vendor/doctrine/orm/bin/doctrine.php orm:clear-cache:metadata',
                'initMessage' => 'Clearing entities cache...',
                'errorMessage' => 'Error clearing entities cache.',
                'failOnError' => false,
            ],
        ],
    ],

];
