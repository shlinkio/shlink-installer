<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config;

use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Exception\InvalidServiceException;

use function get_debug_type;
use function sprintf;

/**
 * @extends AbstractPluginManager<Option\ConfigOptionInterface>
 * @todo Extend from AbstractSingleInstancePluginManager once servicemanager 3 is no longer supported
 */
class ConfigOptionsManager extends AbstractPluginManager implements ConfigOptionsManagerInterface
{
    /** @var class-string<Option\ConfigOptionInterface> */
    protected $instanceOf = Option\ConfigOptionInterface::class; // phpcs:ignore

    public function validate(mixed $instance): void
    {
        if ($instance instanceof $this->instanceOf) {
            return;
        }

        throw new InvalidServiceException(sprintf(
            'Plugin manager "%s" expected an instance of type "%s", but "%s" was received',
            static::class,
            $this->instanceOf,
            get_debug_type($instance),
        ));
    }
}
