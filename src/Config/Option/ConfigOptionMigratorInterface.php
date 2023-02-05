<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

interface ConfigOptionMigratorInterface
{
    /**
     * This method can be used on config options that need to potentially migrate deprecated values to their
     * corresponding replacements
     */
    public function tryToMigrateValue(mixed $currentValue): mixed;
}
