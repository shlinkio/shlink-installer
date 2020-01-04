<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config;

use Psr\Container\ContainerInterface;

class ConfigGeneratorFactory
{
    public function __invoke(ContainerInterface $container): ConfigGenerator
    {
        $config = $container->get('config');
        $configOptions = $config['config_options'] ?? [];
        $enabledOptions = $config['installer']['enabled_options'] ?? null;
        $configOptionsManager = new ConfigOptionsManager($container, $configOptions);

        return new ConfigGenerator($configOptionsManager, $configOptions['groups'] ?? [], $enabledOptions);
    }
}
