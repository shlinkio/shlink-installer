<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Shlinkio\Shlink\Installer\Util\AskUtilsTrait;
use Symfony\Component\Console\Style\SymfonyStyle;

class DatabaseUserConfigOption extends AbstractDatabaseDriverDependentConfigOption
{
    use AskUtilsTrait;

    public function getConfigPath(): array
    {
        return ['entity_manager', 'connection', 'user'];
    }

    public function ask(SymfonyStyle $io, array $currentOptions)
    {
        return $this->askRequired($io, 'username', 'Database username');
    }
}
