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
        $commandMap = $container->get('config')['installer']['commands'] ?? [];
        $app = new Application(
            'Shlink installer',
            InstalledVersions::getPrettyVersion('shlinkio/shlink-installer') ?? '',
        );

        $app->setCommandLoader(new ContainerCommandLoader($container, $commandMap));

        return $app;
    }
}
