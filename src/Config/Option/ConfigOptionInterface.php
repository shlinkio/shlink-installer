<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Shlinkio\Shlink\Installer\Util\PathCollection;
use Symfony\Component\Console\Style\SymfonyStyle;

interface ConfigOptionInterface
{
    public function getConfigPath(): array;

    // FIXME Add ?ConfigOptionInterface $dependentOption = null
    public function shouldBeAsked(PathCollection $currentOptions): bool;

    /**
     * @return mixed
     */
    public function ask(SymfonyStyle $io, PathCollection $currentOptions);
}
