<?php
declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Service;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Process\PhpExecutableFinder;

class InstallationCommandsRunnerFactory
{
    public function __invoke(ContainerInterface $container): InstallationCommandsRunner
    {
        $config = $container->get('config');

        return new InstallationCommandsRunner(
            $container->get(ProcessHelper::class),
            $container->get(PhpExecutableFinder::class),
            $config['installation_commands'] ?? []
        );
    }
}
