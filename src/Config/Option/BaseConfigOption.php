<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Override;

use function array_key_exists;

abstract class BaseConfigOption implements ConfigOptionInterface
{
    #[Override]
    public function shouldBeAsked(array $currentOptions): bool
    {
        return !array_key_exists($this->getEnvVar(), $currentOptions);
    }
}
