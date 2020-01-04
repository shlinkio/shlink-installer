<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Command;

use Shlinkio\Shlink\Installer\Config\ConfigGeneratorInterface;
use Shlinkio\Shlink\Installer\Config\Option\DatabaseDriverConfigOption;
use Shlinkio\Shlink\Installer\Model\ImportedConfig;
use Shlinkio\Shlink\Installer\Service\InstallationCommandsRunnerInterface;
use Shlinkio\Shlink\Installer\Util\AskUtilsTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Zend\Config\Writer\WriterInterface;

use function Functional\every;
use function Functional\tail;
use function sprintf;

class InstallCommand extends Command
{
    use AskUtilsTrait;

    public const GENERATED_CONFIG_PATH = 'config/params/generated_config.php';
    private const POST_INSTALL_COMMANDS = [
        'db_create_schema',
        'db_migrate',
        'orm_proxies',
        'geolite_download',
    ];
    private const SQLITE_DB_PATH = 'data/database.sqlite';

    /** @var WriterInterface */
    private $configWriter;
    /** @var Filesystem */
    private $filesystem;
    /** @var ConfigGeneratorInterface */
    private $configGenerator;
    /** @var bool */
    private $isUpdate;
    /** @var InstallationCommandsRunnerInterface */
    private $commandsRunner;

    /**
     * @throws LogicException
     */
    public function __construct(
        WriterInterface $configWriter,
        Filesystem $filesystem,
        ConfigGeneratorInterface $configGenerator,
        InstallationCommandsRunnerInterface $commandsRunner,
        bool $isUpdate
    ) {
        parent::__construct();
        $this->configWriter = $configWriter;
        $this->filesystem = $filesystem;
        $this->configGenerator = $configGenerator;
        $this->commandsRunner = $commandsRunner;
        $this->isUpdate = $isUpdate;
    }

    protected function configure(): void
    {
        $this
            ->setName('shlink:install')
            ->setDescription('Installs or updates Shlink');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->writeln([
            '<info>Welcome to Shlink!!</info>',
            'This tool will guide you through the installation process.',
        ]);

        // Check if a cached config file exists and drop it if so
        if ($this->filesystem->exists('data/cache/app_config.php')) {
            $io->write('Deleting old cached config...');
            try {
                $this->filesystem->remove('data/cache/app_config.php');
                $io->writeln(' <info>Success</info>');
            } catch (IOException $e) {
                $io->error(
                    'Failed! You will have to manually delete the data/cache/app_config.php file to'
                    . ' get new config applied.'
                );
                if ($io->isVerbose()) {
                    $this->getApplication()->renderThrowable($e, $output);
                }
                return 1;
            }
        }

        $importedConfig = $this->resolvePreviousConfig($io);
        $config = $this->configGenerator->generateConfigInteractively($io, $importedConfig->importedConfig());
        $this->importSqliteIfNeeded($io, $importedConfig->importPath(), $config->getValueInPath(
            DatabaseDriverConfigOption::CONFIG_PATH
        ));

        // Generate config params files
        $this->configWriter->toFile(self::GENERATED_CONFIG_PATH, $config->toArray(), false);
        $io->writeln(['<info>Custom configuration properly generated!</info>', '']);

        if ($this->execPostInstallCommands($io)) {
            $io->success('Installation complete!');
            return 0;
        }

        return -1;
    }

    private function resolvePreviousConfig(SymfonyStyle $io): ImportedConfig
    {
        if (! $this->isUpdate) {
            return ImportedConfig::notImported();
        }

        // Ask the user if he/she wants to import an older configuration
        $importConfig = $io->confirm(
            'Do you want to import configuration from previous installation? (You will still be asked for any new '
            . 'config option that did not exist in previous shlink versions)'
        );
        if (! $importConfig) {
            return ImportedConfig::notImported();
        }

        // Ask the user for the older shlink path
        $keepAsking = true;
        do {
            $installationPath = $this->askRequired(
                $io,
                'previous installation path',
                'Previous shlink installation path from which to import config'
            );
            $configFile = sprintf('%s/%s', $installationPath, self::GENERATED_CONFIG_PATH);
            $configExists = $this->filesystem->exists($configFile);

            if (! $configExists) {
                $keepAsking = $io->confirm(
                    'Provided path does not seem to be a valid shlink root path. Do you want to try another path?'
                );
            }
        } while (! $configExists && $keepAsking);

        // If after some retries the user has chosen not to test another path, return
        if (! $configExists) {
            return ImportedConfig::notImported();
        }

        // Read the config file
        return ImportedConfig::imported($installationPath, include $configFile);
    }

    private function importSqliteIfNeeded(SymfonyStyle $io, string $importPath, ?string $dbDriver): void
    {
        if (! $this->isUpdate || $dbDriver !== DatabaseDriverConfigOption::SQLITE_DRIVER) {
            return;
        }

        try {
            $this->filesystem->copy($importPath . '/' . self::SQLITE_DB_PATH, self::SQLITE_DB_PATH);
        } catch (IOException $e) {
            $io->error('It wasn\'t possible to import the SQLite database');
            throw $e;
        }
    }

    private function execPostInstallCommands(SymfonyStyle $io): bool
    {
        $commands = $this->isUpdate ? tail(self::POST_INSTALL_COMMANDS) : self::POST_INSTALL_COMMANDS;

        return every($commands, function (string $commandName) use ($io) {
            return $this->commandsRunner->execPhpCommand($commandName, $io);
        });
    }
}
