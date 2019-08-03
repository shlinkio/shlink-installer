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
    return file_exists($appConfigPath) ? require $appConfigPath : [];
})();
$localConfig = (function () {
    $localConfig = __DIR__ . '/config.local.php';
    return file_exists($localConfig) ? require $localConfig : [];
})();

$config = array_reduce([$appConfig, $installerConfig, $localConfig], [ArrayUtils::class, 'merge'], []);
$container = new ServiceManager($config['dependencies']);
$container->setService('config', $config);

return $container;
