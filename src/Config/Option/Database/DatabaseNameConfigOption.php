<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Database;

use Symfony\Component\Console\Style\StyleInterface;

class DatabaseNameConfigOption extends AbstractNonSqliteDependentConfigOption
{
    public function getEnvVar(): string
    {
        return 'DB_NAME';
    }

    public function ask(StyleInterface $io, array $currentOptions): string
    {
        return $io->ask('Database name', 'shlink');
    }
}
