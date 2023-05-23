<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Command;

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
    private readonly FlagOption $updateRoadRunnerBin;
    private readonly FlagOption $skipDownloadGeoLiteDb;

    public function __construct(private readonly InstallationCommandsRunnerInterface $commandsRunner)
    {
        parent::__construct();

        $this->skipInitDb = new FlagOption(
            $this,
            'skip-initialize-db',
            'Skip the initial empty database creation. It will make this command fail on a later stage if the '
            . 'database was not created manually.',
        );
        $this->clearDbCache = new FlagOption($this, 'clear-db-cache', 'Clear the database metadata cache.');
        $this->initialApiKey = new FlagOption($this, 'initial-api-key', 'Create and print initial admin API key.');
        $this->updateRoadRunnerBin = new FlagOption(
            $this,
            'download-rr-binary',
            'Download a RoadRunner binary. Useful only if you plan to serve Shlink with Roadrunner.',
        );
        $this->skipDownloadGeoLiteDb = new FlagOption(
            $this,
            'skip-download-geolite',
            'Skip downloading the initial GeoLite DB file. Shlink will try to download it the first time it needs to '
            . 'geolocate visits.',
        );
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
            updateRoadrunnerBinary: $this->updateRoadRunnerBin->get($input),
            generateApiKey: $this->initialApiKey->get($input),
            downloadGeoLiteDb: ! $this->skipDownloadGeoLiteDb->get($input),
        );
        $commands = InstallationCommand::resolveCommandsForConfig($config);
        $io = new SymfonyStyle($input, $output);

        return every(
            $commands,
            fn (InstallationCommand $command) => $this->commandsRunner->execPhpCommand($command->value, $io),
        ) ? 0 : -1;
    }
}
