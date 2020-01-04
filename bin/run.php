<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer;

use Symfony\Component\Console\Application;
use Zend\ServiceManager\ServiceLocatorInterface;

/** @var ServiceLocatorInterface $container */
$container = include __DIR__ . '/../config/container.php';

return [
    fn () => $container->build(Application::class, ['isUpdate' => false])->run(),
    fn () => $container->build(Application::class, ['isUpdate' => true])->run(),
];
