<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Symfony\Component\Console\Style\StyleInterface;

interface ConfigOptionInterface
{
    public function getEnvVar(): string;

    public function shouldBeAsked(array $currentOptions): bool;

    public function ask(StyleInterface $io, array $currentOptions): mixed;
}
