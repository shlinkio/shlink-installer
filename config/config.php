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
                Config\Option\DatabaseDriverConfigOption::class,
                Config\Option\DatabaseNameConfigOption::class,
                Config\Option\DatabaseHostConfigOption::class,
                Config\Option\DatabasePortConfigOption::class,
                Config\Option\DatabaseUserConfigOption::class,
                Config\Option\DatabasePasswordConfigOption::class,
                Config\Option\DatabaseSqlitePathConfigOption::class,
                Config\Option\DatabaseMySqlOptionsConfigOption::class,
            ],
            'URL SHORTENER' => [
                Config\Option\ShortDomainHostConfigOption::class,
                Config\Option\ShortDomainSchemaConfigOption::class,
                Config\Option\ValidateUrlConfigOption::class,
                Config\Option\VisitsWebhooksConfigOption::class,
                Config\Option\ShortCodeLengthOption::class,
            ],
            'REDIRECTS' => [
                Config\Option\BaseUrlRedirectConfigOption::class,
                Config\Option\InvalidShortUrlRedirectConfigOption::class,
                Config\Option\Regular404RedirectConfigOption::class,
            ],
            'APPLICATION' => [
                Config\Option\DisableTrackParamConfigOption::class,
                Config\Option\CheckVisitsThresholdConfigOption::class,
                Config\Option\VisitsThresholdConfigOption::class,
                Config\Option\BasePathConfigOption::class,
                Config\Option\TaskWorkerNumConfigOption::class,
                Config\Option\WebWorkerNumConfigOption::class,
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
            Config\Option\CheckVisitsThresholdConfigOption::class => InvokableFactory::class,
            Config\Option\VisitsThresholdConfigOption::class => InvokableFactory::class,
            Config\Option\DatabaseDriverConfigOption::class => InvokableFactory::class,
            Config\Option\DatabaseNameConfigOption::class => InvokableFactory::class,
            Config\Option\DatabaseHostConfigOption::class => InvokableFactory::class,
            Config\Option\DatabasePortConfigOption::class => InvokableFactory::class,
            Config\Option\DatabaseUserConfigOption::class => InvokableFactory::class,
            Config\Option\DatabasePasswordConfigOption::class => InvokableFactory::class,
            Config\Option\DatabaseSqlitePathConfigOption::class => InvokableFactory::class,
            Config\Option\DatabaseMySqlOptionsConfigOption::class => InvokableFactory::class,
            Config\Option\DisableTrackParamConfigOption::class => InvokableFactory::class,
            Config\Option\BaseUrlRedirectConfigOption::class => InvokableFactory::class,
            Config\Option\InvalidShortUrlRedirectConfigOption::class => InvokableFactory::class,
            Config\Option\Regular404RedirectConfigOption::class => InvokableFactory::class,
            Config\Option\ShortDomainHostConfigOption::class => InvokableFactory::class,
            Config\Option\ShortDomainSchemaConfigOption::class => InvokableFactory::class,
            Config\Option\ValidateUrlConfigOption::class => InvokableFactory::class,
            Config\Option\RedisServersConfigOption::class => InvokableFactory::class,
            Config\Option\VisitsWebhooksConfigOption::class => ConfigAbstractFactory::class,
            Config\Option\TaskWorkerNumConfigOption::class => ConfigAbstractFactory::class,
            Config\Option\WebWorkerNumConfigOption::class => ConfigAbstractFactory::class,
            Config\Option\ShortCodeLengthOption::class => InvokableFactory::class,
            Config\Option\Mercure\EnableMercureConfigOption::class => ConfigAbstractFactory::class,
            Config\Option\Mercure\MercurePublicUrlConfigOption::class => ConfigAbstractFactory::class,
            Config\Option\Mercure\MercureInternalUrlConfigOption::class => ConfigAbstractFactory::class,
            Config\Option\Mercure\MercureJwtSecretConfigOption::class => ConfigAbstractFactory::class,
        ],
    ],

    ConfigAbstractFactory::class => [
        Config\Option\VisitsWebhooksConfigOption::class => [Factory\SwooleInstalledFactory::SWOOLE_INSTALLED],
        Config\Option\TaskWorkerNumConfigOption::class => [Factory\SwooleInstalledFactory::SWOOLE_INSTALLED],
        Config\Option\WebWorkerNumConfigOption::class => [Factory\SwooleInstalledFactory::SWOOLE_INSTALLED],
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
            ],
            'db_migrate' => [
                'command' => 'vendor/doctrine/migrations/bin/doctrine-migrations.php migrations:migrate',
                'initMessage' => 'Updating database...',
                'errorMessage' => 'Error updating database.',
            ],
            'orm_proxies' => [
                'command' => 'vendor/doctrine/orm/bin/doctrine.php orm:generate-proxies',
                'initMessage' => 'Generating proxies...',
                'errorMessage' => 'Error generating proxies.',
            ],
            'orm_clear_cache' => [
                'command' => 'vendor/doctrine/orm/bin/doctrine.php orm:clear-cache:metadata',
                'initMessage' => 'Clearing entities cache...',
                'errorMessage' => 'Error clearing entities cache.',
            ],
        ],
    ],

];
