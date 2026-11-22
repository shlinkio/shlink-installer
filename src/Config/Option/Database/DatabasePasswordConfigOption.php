<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Database;

use Override;
use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidator;
use Symfony\Component\Console\Style\StyleInterface;

class DatabasePasswordConfigOption extends AbstractNonSqliteDependentConfigOption
{
    #[Override]
    public function getEnvVar(): string
    {
        return 'DB_PASSWORD';
    }

    #[Override]
    public function ask(StyleInterface $io, array $currentOptions): string
    {
        return $io->ask(
            'Database password',
            validator: static fn ($value) => ConfigOptionsValidator::validateRequired($value, 'password'),
        );
    }
}
