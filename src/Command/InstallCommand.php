<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Command;

use Shlinkio\Shlink\Installer\Service\InstallationRunnerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class InstallCommand extends Command
{
    public const string NAME = 'install';

    public function __construct(private readonly InstallationRunnerInterface $installationRunner)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(self::NAME)
            ->setDescription('Guides you through the installation process, to get Shlink up and running.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $initCommand = $this->getApplication()?->find(InitCommand::NAME);

        return $this->installationRunner->runInstallation($initCommand, $io);
    }
}
