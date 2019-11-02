<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Command;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Shlinkio\Shlink\Installer\Config\ConfigCustomizerManagerInterface;
use Shlinkio\Shlink\Installer\Config\Plugin;
use Shlinkio\Shlink\Installer\Model\CustomizableAppConfig;
use Shlinkio\Shlink\Installer\Service\InstallationCommandsRunnerInterface;
use Shlinkio\Shlink\Installer\Util\AskUtilsTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Exception\RuntimeException;
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

    /** @var WriterInterface */
    private $configWriter;
    /** @var Filesystem */
    private $filesystem;
    /** @var ConfigCustomizerManagerInterface */
    private $configCustomizers;
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
        ConfigCustomizerManagerInterface $configCustomizers,
        InstallationCommandsRunnerInterface $commandsRunner,
        bool $isUpdate
    ) {
        parent::__construct();
        $this->configWriter = $configWriter;
        $this->filesystem = $filesystem;
        $this->configCustomizers = $configCustomizers;
        $this->commandsRunner = $commandsRunner;
        $this->isUpdate = $isUpdate;
    }

    protected function configure(): void
    {
        $this
            ->setName('shlink:install')
            ->setDescription('Installs or updates Shlink');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
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
                    $this->getApplication()->renderException($e, $output);
                }
                return;
            }
        }

        $config = $this->resolveConfig($io);

        // Ask for custom config params
        $plugins = [
            Plugin\DatabaseConfigCustomizer::class,
            Plugin\UrlShortenerConfigCustomizer::class,
            Plugin\RedirectsConfigCustomizer::class,
            Plugin\ApplicationConfigCustomizer::class,
        ];
        foreach ($plugins as $pluginName) {
            /** @var Plugin\ConfigCustomizerInterface $configCustomizer */
            $configCustomizer = $this->configCustomizers->get($pluginName);
            $configCustomizer->process($io, $config);
        }

        // Generate config params files
        $this->configWriter->toFile(self::GENERATED_CONFIG_PATH, $config->getArrayCopy(), false);
        $io->writeln(['<info>Custom configuration properly generated!</info>', '']);

        if ($this->execPostInstallCommands($io)) {
            $io->success('Installation complete!');
        }
    }

    /**
     * @throws RuntimeException
     */
    private function resolveConfig(SymfonyStyle $io): CustomizableAppConfig
    {
        $config = new CustomizableAppConfig();

        if (! $this->isUpdate) {
            return $config;
        }

        // Ask the user if he/she wants to import an older configuration
        $importConfig = $io->confirm(
            'Do you want to import configuration from previous installation? (You will still be asked for any new '
            . 'config option that did not exist in previous shlink versions)'
        );
        if (! $importConfig) {
            return $config;
        }

        // Ask the user for the older shlink path
        $keepAsking = true;
        do {
            $config->setImportedInstallationPath($this->askRequired(
                $io,
                'previous installation path',
                'Previous shlink installation path from which to import config'
            ));
            $configFile = sprintf('%s/%s', $config->getImportedInstallationPath(), self::GENERATED_CONFIG_PATH);
            $configExists = $this->filesystem->exists($configFile);

            if (! $configExists) {
                $keepAsking = $io->confirm(
                    'Provided path does not seem to be a valid shlink root path. Do you want to try another path?'
                );
            }
        } while (! $configExists && $keepAsking);

        // If after some retries the user has chosen not to test another path, return
        if (! $configExists) {
            return $config;
        }

        // Read the config file
        $config->exchangeArray(include $configFile);
        return $config;
    }

    private function execPostInstallCommands(SymfonyStyle $io): bool
    {
        $commands = $this->isUpdate ? tail(self::POST_INSTALL_COMMANDS) : self::POST_INSTALL_COMMANDS;

        return every($commands, function (string $commandName) use ($io) {
            return $this->commandsRunner->execPhpCommand($commandName, $io);
        });
    }
}
