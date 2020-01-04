<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Shlinkio\Shlink\Installer\Util\PathCollection;
use Symfony\Component\Console\Style\StyleInterface;

interface ConfigOptionInterface
{
    public function getConfigPath(): array;

    public function shouldBeAsked(PathCollection $currentOptions): bool;

    /**
     * @return mixed
     */
    public function ask(StyleInterface $io, PathCollection $currentOptions);
}
