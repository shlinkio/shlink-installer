<?php
declare(strict_types=1);

namespace Shlinkio\Shlink\Installer;

use Symfony\Component\Console\Application;
use Symfony\Component\Filesystem\Filesystem;
use Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Zend\ServiceManager\Factory\InvokableFactory;

return [

    'dependencies' => [
        'factories' => [
            Application::class => Factory\InstallApplicationFactory::class,
            Filesystem::class => InvokableFactory::class,
            Util\StringGenerator::class => InvokableFactory::class,
        ],
    ],

    'config_customizer_plugins' => [
        'factories' => [
            Config\Plugin\DatabaseConfigCustomizer::class => ConfigAbstractFactory::class,
            Config\Plugin\UrlShortenerConfigCustomizer::class => ConfigAbstractFactory::class,
            Config\Plugin\LanguageConfigCustomizer::class => InvokableFactory::class,
            Config\Plugin\ApplicationConfigCustomizer::class => ConfigAbstractFactory::class,
        ],
    ],

    ConfigAbstractFactory::class => [
        Config\Plugin\DatabaseConfigCustomizer::class => [Filesystem::class],
        Config\Plugin\UrlShortenerConfigCustomizer::class => [Util\StringGenerator::class],
        Config\Plugin\ApplicationConfigCustomizer::class => [Util\StringGenerator::class],
    ],

];
