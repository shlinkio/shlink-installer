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
    public function runInstallation(Command|null $initCommand, SymfonyStyle $io): int;

    /**
     * @return Command::SUCCESS|Command::FAILURE
     */
    public function runUpdate(Command|null $initCommand, SymfonyStyle $io): int;
}
