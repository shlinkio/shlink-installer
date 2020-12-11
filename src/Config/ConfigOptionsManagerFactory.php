<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config;

use Psr\Container\ContainerInterface;

class ConfigOptionsManagerFactory
{
    public function __invoke(ContainerInterface $container): ConfigOptionsManager
    {
        $config = $container->get('config');
        $configOptions = $config['config_options'] ?? [];

        return new ConfigOptionsManager($container, $configOptions);
    }
}
