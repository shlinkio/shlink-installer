<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Command;

class InstallCommand extends AbstractInstallCommand
{
    public const string NAME = 'install';

    protected function configure(): void
    {
        $this
            ->setName(self::NAME)
            ->setDescription('Guides you through the installation process, to get Shlink up and running.');
    }

    protected function isUpdate(): bool
    {
        return false;
    }
}
