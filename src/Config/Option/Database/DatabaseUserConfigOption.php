<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Database;

use Shlinkio\Shlink\Installer\Util\AskUtilsTrait;
use Symfony\Component\Console\Style\StyleInterface;

class DatabaseUserConfigOption extends AbstractNonSqliteDependentConfigOption
{
    use AskUtilsTrait;

    public function getEnvVar(): string
    {
        return 'DB_USER';
    }

    public function ask(StyleInterface $io, array $currentOptions): string
    {
        return $this->askRequired($io, 'username', 'Database username');
    }
}
