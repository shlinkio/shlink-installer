<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Shlinkio\Shlink\Config\Collection\PathCollection;

abstract class BaseConfigOption implements ConfigOptionInterface
{
    public function shouldBeAsked(PathCollection $currentOptions): bool
    {
        $envVarPath = [$this->getEnvVar()];

        // If the config contains the deprecated path, set its value in the new path, and unset the deprecated one
        $deprecatedPath = $this->getDeprecatedPath();
        if ($currentOptions->pathExists($deprecatedPath)) {
            $currentOptions->setValueInPath($currentOptions->getValueInPath($deprecatedPath), $envVarPath);
            $currentOptions->unsetPath($deprecatedPath);
        }

        return ! $currentOptions->pathExists($envVarPath);
    }
}
