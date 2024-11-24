<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Util;

interface ConfigWriterInterface
{
    public function toFile(string $fileName, array $config): void;
}
