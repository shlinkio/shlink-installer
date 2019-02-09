<?php
declare(strict_types=1);

namespace Shlinkio\Shlink\Installer;

use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;
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

$installerConfig = require __DIR__ . '/config.php';
$appConfig = (function () {
    $appConfigPath = __DIR__ . '/../../../../config/config.php';
    if (! file_exists($appConfigPath)) {
        return [];
    };

    $appConfig = require $appConfigPath;
    // Let's avoid service name conflicts
    unset($appConfig['dependencies']);

    return $appConfig;
})();

$config = ArrayUtils::merge($installerConfig, $appConfig);
$container = new ServiceManager($config['dependencies']);
$container->setService('config', $config);

return $container;
