<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Command;

use Shlinkio\Shlink\Installer\Command\Model\InitOption;
use Shlinkio\Shlink\Installer\Model\CLIOption;
use Shlinkio\Shlink\Installer\Model\ShlinkInitConfig;
use Shlinkio\Shlink\Installer\Service\InstallationCommandsRunnerInterface;
use Shlinkio\Shlink\Installer\Util\InstallationCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function array_reduce;

class InitCommand extends Command
{
    public const NAME = 'init';

    private readonly CLIOption $skipInitDb;
    private readonly CLIOption $clearDbCache;
    private readonly CLIOption $initialApiKey;
    private readonly CLIOption $downloadRoadRunnerBin;
    private readonly CLIOption $skipDownloadGeoLiteDb;

    public function __construct(private readonly InstallationCommandsRunnerInterface $commandsRunner)
    {
        parent::__construct();

        $this->initialApiKey = InitOption::INITIAL_API_KEY->toCLIOption($this);
        $this->skipInitDb = InitOption::SKIP_INITIALIZE_DB->toCLIOption($this);
        $this->clearDbCache = InitOption::CLEAR_DB_CACHE->toCLIOption($this);
        $this->downloadRoadRunnerBin = InitOption::DOWNLOAD_RR_BINARY->toCLIOption($this);
        $this->skipDownloadGeoLiteDb = InitOption::SKIP_DOWNLOAD_GEOLITE->toCLIOption($this);
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
        $commands = [...InstallationCommand::resolveCommandsForConfig($config)];
        $io = new SymfonyStyle($input, $output);

        return array_reduce($commands, function (bool $carry, array $commandInfo) use ($input, $io): bool {
            /** @var array{InstallationCommand, string | null} $commandInfo */
            [$command, $arg] = $commandInfo;

            return $this->commandsRunner->execPhpCommand(
                name: $command->value,
                io: $io,
                interactive: $input->isInteractive(),
                args: $arg !== null ? [$arg] : [],
            ) && $carry;
        }, initial: true) ? 0 : -1;
    }
}
