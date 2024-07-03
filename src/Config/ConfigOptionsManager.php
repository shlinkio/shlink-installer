<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config;

use Laminas\ServiceManager\AbstractPluginManager;

/**
 * @extends AbstractPluginManager<Option\ConfigOptionInterface>
 */
class ConfigOptionsManager extends AbstractPluginManager implements ConfigOptionsManagerInterface
{
    protected $instanceOf = Option\ConfigOptionInterface::class; // phpcs:ignore
}
