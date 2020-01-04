<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Shlinkio\Shlink\Installer\Util\PathCollection;
use Symfony\Component\Console\Style\SymfonyStyle;

class BasePathConfigOption extends BaseConfigOption
{
    public function getConfigPath(): array
    {
        return ['router', 'base_path'];
    }

    public function ask(SymfonyStyle $io, PathCollection $currentOptions, ?ConfigOptionInterface $dependentOption)
    {
        return $io->ask(
            'What is the path from which shlink is going to be served? (Leave empty if you plan to serve '
            . 'shlink from the root of the domain)'
        ) ?? '';
    }
}
