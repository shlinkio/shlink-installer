<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Command;

use Shlinkio\Shlink\Installer\Command\Model\InitOption;
use Shlinkio\Shlink\Installer\Model\FlagOption;
use Shlinkio\Shlink\Installer\Model\ShlinkInitConfig;
use Shlinkio\Shlink\Installer\Service\InstallationCommandsRunnerInterface;
use Shlinkio\Shlink\Installer\Util\InstallationCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function Functional\every;

class InitCommand extends Command
{
    public const NAME = 'init';

    private readonly FlagOption $skipInitDb;
    private readonly FlagOption $clearDbCache;
    private readonly FlagOption $initialApiKey;
    private readonly FlagOption $downloadRoadRunnerBin;
    private readonly FlagOption $skipDownloadGeoLiteDb;

    public function __construct(private readonly InstallationCommandsRunnerInterface $commandsRunner)
    {
        parent::__construct();

        $this->skipInitDb = InitOption::SKIP_INITIALIZE_DB->toFlagOption($this);
        $this->clearDbCache = InitOption::CLEAR_DB_CACHE->toFlagOption($this);
        $this->initialApiKey = InitOption::INITIAL_API_KEY->toFlagOption($this);
        $this->downloadRoadRunnerBin = InitOption::DOWNLOAD_RR_BINARY->toFlagOption($this);
        $this->skipDownloadGeoLiteDb = InitOption::SKIP_DOWNLOAD_GEOLITE->toFlagOption($this);
    }

    protected function configure(): void
    {
        $this
            ->setName(self::NAME)
            ->setDescription(
                'Initializes external dependencies required for Shlink to properly work, like DB, cache warmup, '
                . 'initial GeoLite DB download, etc',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $config = new ShlinkInitConfig(
            initializeDb: ! $this->skipInitDb->get($input),
            clearDbCache: $this->clearDbCache->get($input),
            downloadRoadrunnerBinary: $this->downloadRoadRunnerBin->get($input),
            generateApiKey: $this->initialApiKey->get($input),
            downloadGeoLiteDb: ! $this->skipDownloadGeoLiteDb->get($input),
        );
        $commands = InstallationCommand::resolveCommandsForConfig($config);
        $io = new SymfonyStyle($input, $output);

        return every($commands, fn (InstallationCommand $command) => $this->commandsRunner->execPhpCommand(
            $command->value,
            $io,
            $input->isInteractive(),
        )) ? 0 : -1;
    }
}
