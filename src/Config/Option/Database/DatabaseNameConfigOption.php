<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Database;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Symfony\Component\Console\Style\StyleInterface;

class DatabaseNameConfigOption extends AbstractNonSqliteDependentConfigOption
{
    public function getDeprecatedPath(): array
    {
        return ['entity_manager', 'connection', 'dbname'];
    }

    public function getEnvVar(): string
    {
        return 'DB_NAME';
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): string
    {
        return $io->ask('Database name', 'shlink');
    }
}
