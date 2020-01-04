<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

interface DependentConfigOptionInterface
{
    public function getDependentOption(): string;
}
