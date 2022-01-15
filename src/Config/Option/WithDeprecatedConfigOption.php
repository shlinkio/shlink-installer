<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

interface WithDeprecatedConfigOption
{
    /**
     * @deprecated
     */
    public function getDeprecatedPath(): array;
}
