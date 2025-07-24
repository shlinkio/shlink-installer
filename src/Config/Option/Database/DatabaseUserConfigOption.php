<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Database;

use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidator;
use Symfony\Component\Console\Style\StyleInterface;

class DatabaseUserConfigOption extends AbstractNonSqliteDependentConfigOption
{
    public function getEnvVar(): string
    {
        return 'DB_USER';
    }

    public function ask(StyleInterface $io, array $currentOptions): string
    {
        return $io->ask(
            'Database username',
            validator: static fn ($value) => ConfigOptionsValidator::validateRequired($value, 'username'),
        );
    }
}
