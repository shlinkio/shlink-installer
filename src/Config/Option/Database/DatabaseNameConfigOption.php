<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Database;

use Override;
use Symfony\Component\Console\Style\StyleInterface;

class DatabaseNameConfigOption extends AbstractNonSqliteDependentConfigOption
{
    #[Override]
    public function getEnvVar(): string
    {
        return 'DB_NAME';
    }

    #[Override]
    public function ask(StyleInterface $io, array $currentOptions): string
    {
        return $io->ask('Database name', 'shlink');
    }
}
