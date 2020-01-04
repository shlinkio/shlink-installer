<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Shlinkio\Shlink\Installer\Util\PathCollection;

abstract class BaseConfigOption implements ConfigOptionInterface
{
    public function shouldBeAsked(PathCollection $currentOptions): bool
    {
        return ! $currentOptions->pathExists($this->getConfigPath());
    }
}
