<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Util;

/** @deprecated */
interface ExpectedConfigResolverInterface
{
    public function resolveExpectedKeys(string $pluginName, ?array $defaultExpectedKeys = null): array;
}
