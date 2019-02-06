<?php
declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Service;

use Interop\Container\ContainerInterface;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Process\PhpExecutableFinder;
use Zend\ServiceManager\Factory\FactoryInterface;

class InstallationCommandsRunnerFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ): InstallationCommandsRunner {
        $config = $container->get('config');

        return new InstallationCommandsRunner(
            new ProcessHelper(),
            new PhpExecutableFinder(),
            $config['installation_commands'] ?? []
        );
    }
}
