<?php
declare(strict_types=1);

namespace Shlinkio\Shlink\Installer;

use Shlinkio\Shlink\Installer\Factory\ProcessHelperFactory;
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
            Console\Helper\ProcessHelper::class => ProcessHelperFactory::class,

            Util\StringGenerator::class => InvokableFactory::class,
            Service\InstallationCommandsRunner::class => Service\InstallationCommandsRunnerFactory::class,
            Config\Util\ExpectedConfigResolver::class => Config\Util\ExpectedConfigResolverFactory::class,
        ],
    ],

    'config_customizer_plugins' => [
        'factories' => [
            Config\Plugin\DatabaseConfigCustomizer::class => ConfigAbstractFactory::class,
            Config\Plugin\UrlShortenerConfigCustomizer::class => ConfigAbstractFactory::class,
            Config\Plugin\LanguageConfigCustomizer::class => ConfigAbstractFactory::class,
            Config\Plugin\ApplicationConfigCustomizer::class => ConfigAbstractFactory::class,
        ],
    ],

    ConfigAbstractFactory::class => [
        Config\Plugin\DatabaseConfigCustomizer::class => [Config\Util\ExpectedConfigResolver::class, Filesystem::class],
        Config\Plugin\UrlShortenerConfigCustomizer::class => [
            Config\Util\ExpectedConfigResolver::class,
            Util\StringGenerator::class,
        ],
        Config\Plugin\LanguageConfigCustomizer::class => [Config\Util\ExpectedConfigResolver::class],
        Config\Plugin\ApplicationConfigCustomizer::class => [
            Config\Util\ExpectedConfigResolver::class,
            Util\StringGenerator::class,
        ],
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
