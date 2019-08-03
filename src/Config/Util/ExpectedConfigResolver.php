<?php
declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Util;

class ExpectedConfigResolver implements ExpectedConfigResolverInterface
{
    /** @var array */
    private $expectedKeysMap;

    public function __construct(array $expectedKeysMap)
    {
        $this->expectedKeysMap = $expectedKeysMap;
    }

    public function resolveExpectedKeys(string $pluginName, ?array $defaultExpectedKeys = null): array
    {
        return $this->expectedKeysMap[$pluginName] ?? $defaultExpectedKeys ?? [];
    }
}
