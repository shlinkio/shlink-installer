<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Command;

use Shlinkio\Shlink\Installer\Service\InstallationRunnerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateCommand extends Command
{
    public const string NAME = 'update';

    public function __construct(private readonly InstallationRunnerInterface $installationRunner)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(self::NAME)
            ->setDescription('Helps you import Shlink\'s config from an older version to a new one.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $initCommand = $this->getApplication()?->find(InitCommand::NAME);

        return $this->installationRunner->runUpdate($initCommand, $io);
    }
}
