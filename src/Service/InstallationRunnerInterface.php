<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Service;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

interface InstallationRunnerInterface
{
    /**
     * @return Command::SUCCESS|Command::FAILURE
     */
    public function runInstallation(SymfonyStyle $io, Command|null $initCommand): int;

    /**
     * @return Command::SUCCESS|Command::FAILURE
     */
    public function runUpdate(SymfonyStyle $io, Command|null $initCommand): int;
}
