<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Database;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Util\AskUtilsTrait;
use Symfony\Component\Console\Style\StyleInterface;

class DatabasePasswordConfigOption extends AbstractNonSqliteDependentConfigOption
{
    use AskUtilsTrait;

    public function getDeprecatedPath(): array
    {
        return ['entity_manager', 'connection', 'password'];
    }

    public function getEnvVar(): string
    {
        return 'DB_PASSWORD';
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): string
    {
        return $this->askRequired($io, 'password', 'Database password');
    }
}
