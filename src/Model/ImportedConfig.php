<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Model;

final class ImportedConfig
{
    /** @var string */
    private $importPath;
    /** @var array */
    private $importedConfig;

    private function __construct(string $importPath, array $importedConfig)
    {
        $this->importPath = $importPath;
        $this->importedConfig = $importedConfig;
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
