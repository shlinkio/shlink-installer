<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Command;

class UpdateCommand extends AbstractInstallCommand
{
    public const NAME = 'update';

    protected function configure(): void
    {
        $this
            ->setName(self::NAME)
            ->setDescription('Helps you import Shlink\'s config from an older version to a new one.');
    }

    protected function isUpdate(): bool
    {
        return true;
    }
}
