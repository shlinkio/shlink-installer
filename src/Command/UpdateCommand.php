<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Command;

use Shlinkio\Shlink\Installer\Service\InstallationRunnerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(UpdateCommand::NAME, 'Helps you import Shlink\'s config from an older version to a new one')]
class UpdateCommand extends Command
{
    public const string NAME = 'update';

    public function __construct(private readonly InstallationRunnerInterface $installationRunner)
    {
        parent::__construct();
    }

    public function __invoke(SymfonyStyle $io): int
    {
        $initCommand = $this->getApplication()?->find(InitCommand::NAME);
        return $this->installationRunner->runUpdate($io, $initCommand);
    }
}
