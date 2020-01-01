<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Shlinkio\Shlink\Installer\Util\AskUtilsTrait;
use Symfony\Component\Console\Style\SymfonyStyle;

class DatabasePasswordConfigOption extends AbstractDatabaseDriverDependentConfigOption
{
    use AskUtilsTrait;

    public function getConfigPath(): array
    {
        return ['entity_manager', 'connection', 'password'];
    }

    public function ask(SymfonyStyle $io, array $currentOptions)
    {
        return $this->askRequired($io, 'password', 'Database password');
    }
}
