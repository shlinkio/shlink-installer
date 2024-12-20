<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Database;

use Symfony\Component\Console\Style\StyleInterface;

class DatabaseUseEncryptionConfigOption extends AbstractNonSqliteDependentConfigOption
{
    public function getEnvVar(): string
    {
        return 'DB_USE_ENCRYPTION';
    }

    public function ask(StyleInterface $io, array $currentOptions): bool
    {
        return $io->confirm(
            'Do you want the database connection to be encrypted? Enabling this will make database connections fail if '
            . 'your database server does not support or enforce encryption.',
            default: false,
        );
    }
}
