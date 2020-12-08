<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer;

use Laminas\ServiceManager\ServiceLocatorInterface;
use Symfony\Component\Console\Application;

/** @var ServiceLocatorInterface $container */
$container = include __DIR__ . '/../config/container.php';
$runApp = static fn (bool $isUpdate) => $container->build(Application::class, ['isUpdate' => $isUpdate])->run();

return [static fn () => $runApp(false), static fn () => $runApp(true)];
