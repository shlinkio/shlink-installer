<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer;

use Symfony\Component\Console;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\PhpExecutableFinder;
use Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Zend\ServiceManager\Factory\InvokableFactory;

return [

    'dependencies' => [
        'factories' => [
            Console\Application::class => Factory\InstallApplicationFactory::class,
            Filesystem::class => InvokableFactory::class,
            PhpExecutableFinder::class => InvokableFactory::class,
            Console\Helper\ProcessHelper::class => Factory\ProcessHelperFactory::class,

            Util\StringGenerator::class => InvokableFactory::class,
            Service\InstallationCommandsRunner::class => Service\InstallationCommandsRunnerFactory::class,
            Config\ConfigGenerator::class => Config\ConfigGeneratorFactory::class,
            Config\Util\ExpectedConfigResolver::class => Config\Util\ExpectedConfigResolverFactory::class,
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
            Config\Option\VisitsWebhooksConfigOption::class => ConfigAbstractFactory::class,
            Config\Option\TaskWorkerNumConfigOption::class => ConfigAbstractFactory::class,
            Config\Option\WebWorkerNumConfigOption::class => ConfigAbstractFactory::class,
        ],
    ],

    ConfigAbstractFactory::class => [
        Config\Option\VisitsWebhooksConfigOption::class => [Factory\SwooleInstalledFactory::SWOOLE_INSTALLED],
        Config\Option\TaskWorkerNumConfigOption::class => [Factory\SwooleInstalledFactory::SWOOLE_INSTALLED],
        Config\Option\WebWorkerNumConfigOption::class => [Factory\SwooleInstalledFactory::SWOOLE_INSTALLED],
    ],

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
        'geolite_download' => [
            'command' => 'bin/cli visit:update-db',
            'initMessage' => 'Downloading GeoLite2 db...',
            'errorMessage' => 'Error downloading GeoLite2 db.',
        ],
    ],

];
