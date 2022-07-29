<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

abstract class BaseConfigOption implements ConfigOptionInterface
{
    public function shouldBeAsked(array $currentOptions): bool
    {
        return ! isset($currentOptions[$this->getEnvVar()]);
    }
}
