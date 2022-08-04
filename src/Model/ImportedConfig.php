<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Model;

final class ImportedConfig
{
    private function __construct(public readonly string $importPath, public readonly array $importedConfig)
    {
    }

    public static function notImported(): self
    {
        return new self('', []);
    }

    public static function imported(string $importPath, array $importedConfig): self
    {
        return new self($importPath, $importedConfig);
    }
}
