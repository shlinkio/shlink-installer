<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Factory;

use Laminas\Config\Writer\PhpArray as PhpArrayConfigWriter;
use Psr\Container\ContainerInterface;
use Shlinkio\Shlink\Installer\Command\InstallCommand;
use Shlinkio\Shlink\Installer\Config\ConfigGenerator;
use Shlinkio\Shlink\Installer\Service\InstallationCommandsRunner;
use Shlinkio\Shlink\Installer\Service\ShlinkAssetsHandler;
use Symfony\Component\Console\Application;

class InstallApplicationFactory
{
    public function __invoke(ContainerInterface $container, string $requestedName, ?array $options = null): Application
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
            $container->get(PhpArrayConfigWriter::class),
            $container->get(ShlinkAssetsHandler::class),
            $container->get(ConfigGenerator::class),
            $container->get(InstallationCommandsRunner::class),
            $isUpdate ? 'update' : 'install',
        );
    }
}
