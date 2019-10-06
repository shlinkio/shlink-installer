<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Util;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ExpectedConfigResolverFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $expectedConfigMap = $container->get('config')['installer_plugins_expected_config'] ?? [];
        return new ExpectedConfigResolver($expectedConfigMap);
    }
}
