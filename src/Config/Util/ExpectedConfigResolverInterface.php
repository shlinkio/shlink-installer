<?php
declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Util;

interface ExpectedConfigResolverInterface
{
    public function resolveExpectedKeys(string $pluginName, array $defaultExpectedKeys = null): array;
}
