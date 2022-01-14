<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Symfony\Component\Console\Style\StyleInterface;

interface ConfigOptionInterface
{
    /**
     * @deprecated
     */
    public function getDeprecatedPath(): array;

    public function getEnvVar(): string;

    public function shouldBeAsked(PathCollection $currentOptions): bool;

    public function ask(StyleInterface $io, PathCollection $currentOptions): mixed;
}
