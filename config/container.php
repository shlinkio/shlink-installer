<?php
declare(strict_types=1);

namespace Shlinkio\Shlink\Installer;

use Zend\ServiceManager\ServiceManager;

require __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/config.php';
$container = new ServiceManager($config['dependencies']);
$container->setService('config', $config);

return $container;
