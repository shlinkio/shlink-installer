<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config;

use Laminas\ServiceManager\AbstractSingleInstancePluginManager;
use Laminas\ServiceManager\Exception\InvalidServiceException;

use function get_debug_type;
use function sprintf;

/**
 * @extends AbstractSingleInstancePluginManager<Option\ConfigOptionInterface>
 */
class ConfigOptionsManager extends AbstractSingleInstancePluginManager implements ConfigOptionsManagerInterface
{
    /** @var class-string<Option\ConfigOptionInterface> */
    protected string $instanceOf = Option\ConfigOptionInterface::class; // phpcs:ignore

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
