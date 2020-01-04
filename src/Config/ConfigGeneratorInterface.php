<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config;

use Symfony\Component\Console\Style\SymfonyStyle;

interface ConfigGeneratorInterface
{
    public function generateConfigInteractively(SymfonyStyle $io, array $previousConfig): array;
}
