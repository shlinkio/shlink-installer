<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer;

use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ConfigAggregator\PhpFileProvider;
use Laminas\ServiceManager\ServiceManager;
use Shlinkio\Shlink\Config;

use function file_exists;

$autoloadFiles = [
    __DIR__ . '/../../../autoload.php',
    __DIR__ . '/../vendor/autoload.php',
];
foreach ($autoloadFiles as $autoloadFile) {
    if (file_exists($autoloadFile)) {
        require_once $autoloadFile;
        break;
    }
}

$shlinkConfigLoader = static function () {
    $appConfigPath = __DIR__ . '/../../../../config/config.php';
    if (! file_exists($appConfigPath)) {
        return [];
    }

    $appConfig = require $appConfigPath;
    // Let's avoid service name conflicts
    unset($appConfig['dependencies']);

    return $appConfig;
};

$config = (new ConfigAggregator([
    Config\ConfigProvider::class,
    new PhpFileProvider(__DIR__ . '/config.php'),       // Installer config
    $shlinkConfigLoader,                                // Overwritten config coming from Shlink
    new PhpFileProvider(__DIR__ . '/config.local.php'), // Local config
]))->getMergedConfig();

$container = new ServiceManager($config['dependencies']);
$container->setService('config', $config);

return $container;
