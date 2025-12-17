<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Model;

final readonly class ImportedConfig
{
    private function __construct(public string $importPath, public array $importedConfig)
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
