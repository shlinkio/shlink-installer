<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Command;

use Laminas\Config\Writer\WriterInterface;
use Shlinkio\Shlink\Installer\Config\ConfigGeneratorInterface;
use Shlinkio\Shlink\Installer\Model\ImportedConfig;
use Shlinkio\Shlink\Installer\Service\InstallationCommandsRunnerInterface;
use Shlinkio\Shlink\Installer\Service\ShlinkAssetsHandler;
use Shlinkio\Shlink\Installer\Service\ShlinkAssetsHandlerInterface;
use Shlinkio\Shlink\Installer\Util\InstallationCommand;
use Shlinkio\Shlink\Installer\Util\Utils;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function Functional\every;

abstract class AbstractInstallCommand extends Command
{
    public function __construct(
        private WriterInterface $configWriter,
        private ShlinkAssetsHandlerInterface $assetsHandler,
        private ConfigGeneratorInterface $configGenerator,
        private InstallationCommandsRunnerInterface $commandsRunner,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->text([
            '<info>Welcome to Shlink!!</info>',
            'This tool will guide you through the installation process.',
        ]);

        // Check if a cached config file exists and drop it if so
        $this->assetsHandler->dropCachedConfigIfAny($io);

        $importedConfig = $this->resolvePreviousConfig($io);
        if ($this->isUpdate()) {
            $this->assetsHandler->importShlinkAssetsFromPath($io, $importedConfig->importPath);
        }
        $config = $this->configGenerator->generateConfigInteractively($io, $importedConfig->importedConfig);
        $normalizedConfig = Utils::normalizeAndKeepEnvVarKeys($config);

        // Generate config params files
        $this->configWriter->toFile(ShlinkAssetsHandler::GENERATED_CONFIG_PATH, $normalizedConfig, false);
        $io->text('<info>Custom configuration properly generated!</info>');
        $io->newLine();

        if (! $this->execPostInstallCommands($io)) {
            return -1;
        }

        $io->success('Installation complete!');
        return 0;
    }

    private function resolvePreviousConfig(SymfonyStyle $io): ImportedConfig
    {
        if ($this->isUpdate()) {
            return $this->assetsHandler->resolvePreviousConfig($io);
        }

        return ImportedConfig::notImported();
    }

    private function execPostInstallCommands(SymfonyStyle $io): bool
    {
        $commands = $this->isUpdate()
            ? InstallationCommand::POST_UPDATE_COMMANDS
            : InstallationCommand::POST_INSTALL_COMMANDS;

        return every(
            $commands,
            fn (InstallationCommand $command) => $this->commandsRunner->execPhpCommand($command->value, $io),
        );
    }

    abstract protected function isUpdate(): bool;
}
