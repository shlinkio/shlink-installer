<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer;

use Laminas\ServiceManager\ServiceLocatorInterface;
use Symfony\Component\Console\Application;

/** @var ServiceLocatorInterface $container */
$container = include __DIR__ . '/../config/container.php';
$runApp = static fn (?string $command = null) => $container->build(Application::class, ['command' => $command])->run();

return [
    static fn () => $runApp(Command\InstallCommand::NAME),
    static fn () => $runApp(Command\UpdateCommand::NAME),
    $runApp,
];
