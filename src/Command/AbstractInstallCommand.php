<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Command;

use Laminas\Config\Writer\WriterInterface;
use Shlinkio\Shlink\Installer\Config\ConfigGeneratorInterface;
use Shlinkio\Shlink\Installer\Model\ImportedConfig;
use Shlinkio\Shlink\Installer\Service\ShlinkAssetsHandler;
use Shlinkio\Shlink\Installer\Service\ShlinkAssetsHandlerInterface;
use Shlinkio\Shlink\Installer\Util\Utils;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractInstallCommand extends Command
{
    public function __construct(
        private readonly WriterInterface $configWriter,
        private readonly ShlinkAssetsHandlerInterface $assetsHandler,
        private readonly ConfigGeneratorInterface $configGenerator,
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

        if (! $this->execInitCommand($io, $importedConfig)) {
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

    private function execInitCommand(SymfonyStyle $io, ImportedConfig $importedConfig): bool
    {
        $isUpdate = $this->isUpdate();
        $input = [
            '--skip-initialize-db' => $isUpdate,
            '--clear-db-cache' => $isUpdate,
            '--initial-api-key' => ! $isUpdate,
            '--download-rr-binary' =>
                $isUpdate && $this->assetsHandler->roadRunnerBinaryExistsInPath($importedConfig->importPath),
        ];

        $command = $this->getApplication()?->find(InitCommand::NAME);
        $exitCode = $command?->run(new ArrayInput($input), $io);

        return $exitCode === 0;
    }

    abstract protected function isUpdate(): bool;
}
