<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Service;

use Shlinkio\Shlink\Installer\Model\ImportedConfig;
use Symfony\Component\Console\Style\StyleInterface;
use Symfony\Component\Filesystem\Exception\IOException;

interface ShlinkAssetsHandlerInterface
{
    /**
     * @throws IOException
     */
    public function dropCachedConfigIfAny(StyleInterface $io): void;

    public function resolvePreviousConfig(StyleInterface $io): ImportedConfig;

    public function importShlinkAssetsFromPath(StyleInterface $io, string $path): void;
}
