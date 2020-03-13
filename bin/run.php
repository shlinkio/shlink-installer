<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer;

use Laminas\ServiceManager\ServiceLocatorInterface;
use Symfony\Component\Console\Application;

/** @var ServiceLocatorInterface $container */
$container = include __DIR__ . '/../config/container.php';
$runApp = fn (bool $isUpdate) => $container->build(Application::class, ['isUpdate' => $isUpdate])->run();

return [fn () => $runApp(false), fn () => $runApp(true)];
