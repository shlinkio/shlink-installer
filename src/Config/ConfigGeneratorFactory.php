<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config;

use Psr\Container\ContainerInterface;

class ConfigGeneratorFactory
{
    public function __invoke(ContainerInterface $container): ConfigGenerator
    {
        $config = $container->get('config')['config_options'];
        $configOptionsManager = new ConfigOptionsManager($container, $config);

        return new ConfigGenerator($configOptionsManager, $config['groups'] ?? []);
    }
}
