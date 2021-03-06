<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Factory;

use Composer\InstalledVersions;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\CommandLoader\ContainerCommandLoader;

class ApplicationFactory
{
    public function __invoke(ContainerInterface $container, string $requestedName, ?array $options = null): Application
    {
        $defaultCommand = $options['command'] ?? null;
        $commandMap = $container->get('config')['installer']['commands'] ?? [];
        $app = new Application('Shlink installer', InstalledVersions::getVersion('shlinkio/shlink-installer'));

        $app->setCommandLoader(new ContainerCommandLoader($container, $commandMap));
        if ($defaultCommand !== null) {
            $app->setDefaultCommand($defaultCommand);
        }

        return $app;
    }
}
