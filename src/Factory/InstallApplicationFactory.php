<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Factory;

use Psr\Container\ContainerInterface;
use Shlinkio\Shlink\Installer\Command\InstallCommand;
use Shlinkio\Shlink\Installer\Config\ConfigCustomizerManager;
use Shlinkio\Shlink\Installer\Service\InstallationCommandsRunner;
use Symfony\Component\Console\Application;
use Symfony\Component\Filesystem\Filesystem;
use Zend\Config\Writer\PhpArray as PhpArrayConfigWriter;

class InstallApplicationFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Application
    {
        $isUpdate = $options !== null && isset($options['isUpdate']) ? (bool) $options['isUpdate'] : false;
        $app = new Application();

        $installCommand = $this->createInstallCommand($container, $isUpdate);
        $app->add($installCommand);
        $app->setDefaultCommand($installCommand->getName(), true);

        return $app;
    }

    private function createInstallCommand(ContainerInterface $container, bool $isUpdate): InstallCommand
    {
        return new InstallCommand(
            new PhpArrayConfigWriter(),
            $container->get(Filesystem::class),
            new ConfigCustomizerManager($container, $container->get('config')['config_customizer_plugins']),
            $container->get(InstallationCommandsRunner::class),
            $isUpdate
        );
    }
}
