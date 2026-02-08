<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Command;

use Shlinkio\Shlink\Installer\Service\InstallationRunnerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(InstallCommand::NAME, 'Guides you through the installation process, to get Shlink up and running')]
class InstallCommand extends Command
{
    public const string NAME = 'install';

    public function __construct(private readonly InstallationRunnerInterface $installationRunner)
    {
        parent::__construct();
    }

    public function __invoke(SymfonyStyle $io): int
    {
        $initCommand = $this->getApplication()?->find(InitCommand::NAME);
        return $this->installationRunner->runInstallation($io, $initCommand);
    }
}
