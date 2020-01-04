<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer;

use Symfony\Component\Console\Application;
use Zend\ServiceManager\ServiceLocatorInterface;

/** @var ServiceLocatorInterface $container */
$container = include __DIR__ . '/../config/container.php';

return [
    static function () use ($container) {
        $container->build(Application::class, ['isUpdate' => false])->run();
    },
    static function () use ($container) {
        $container->build(Application::class, ['isUpdate' => true])->run();
    },
];
