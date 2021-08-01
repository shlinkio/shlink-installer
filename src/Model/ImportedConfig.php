<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Model;

final class ImportedConfig
{
    private function __construct(private string $importPath, private array $importedConfig)
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

    public function importPath(): string
    {
        return $this->importPath;
    }

    public function importedConfig(): array
    {
        return $this->importedConfig;
    }
}
