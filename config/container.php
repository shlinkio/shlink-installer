<?php
declare(strict_types=1);

namespace Shlinkio\Shlink\Installer;

use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;

use function array_reduce;
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
    }

    $appConfig = require $appConfigPath;
    // Let's avoid service name conflicts
    unset($appConfig['dependencies']);

    return $appConfig;
})();
$localConfig = (function () {
    $localConfig = __DIR__ . '/config.local.php';
    return file_exists($localConfig) ? require $localConfig : [];
})();

$config = array_reduce([$installerConfig, $appConfig, $localConfig], [ArrayUtils::class, 'merge'], []);
$container = new ServiceManager($config['dependencies']);
$container->setService('config', $config);

return $container;
