<?php
declare(strict_types=1);

namespace Shlinkio\Shlink\Installer;

use Zend\ServiceManager\ServiceManager;
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

$config = require __DIR__ . '/config.php';
$container = new ServiceManager($config['dependencies']);
$container->setService('config', $config);

return $container;
