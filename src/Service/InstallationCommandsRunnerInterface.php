<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Service;

use Symfony\Component\Console\Style\SymfonyStyle;

interface InstallationCommandsRunnerInterface
{
    public function execPhpCommand(string $name, SymfonyStyle $io): bool;
}
