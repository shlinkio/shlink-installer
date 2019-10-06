<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer;

use Symfony\Component\Console\Application;
use Zend\ServiceManager\ServiceLocatorInterface;

return function (bool $isUpdate) {
    /** @var ServiceLocatorInterface $container */
    $container = include __DIR__ . '/../config/container.php';
    $container->build(Application::class, ['isUpdate' => $isUpdate])->run();
};
