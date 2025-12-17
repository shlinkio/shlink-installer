<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Command;

use Shlinkio\Shlink\Installer\Service\InstallationRunnerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractInstallCommand extends Command
{
    public function __construct(
        private readonly InstallationRunnerInterface $installationRunner,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $initCommand = $this->getApplication()?->find(InitCommand::NAME);

        if ($this->isUpdate()) {
            return $this->installationRunner->runUpdate($initCommand, $io);
        } else {
            return $this->installationRunner->runInstallation($initCommand, $io);
        }
    }

    abstract protected function isUpdate(): bool;
}
