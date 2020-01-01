<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Symfony\Component\Console\Style\SymfonyStyle;

interface ConfigOptionInterface
{
    public function getConfigPath(): array;

    public function shouldBeAsked(array $currentOptions): bool;

    /**
     * @return mixed
     */
    public function ask(SymfonyStyle $io, array $currentOptions);
}
