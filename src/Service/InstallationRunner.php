<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Service;

use Shlinkio\Shlink\Installer\Command\Model\InitOption;
use Shlinkio\Shlink\Installer\Config\ConfigGeneratorInterface;
use Shlinkio\Shlink\Installer\Model\ImportedConfig;
use Shlinkio\Shlink\Installer\Util\ConfigWriterInterface;
use Shlinkio\Shlink\Installer\Util\Utils;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Style\SymfonyStyle;

readonly class InstallationRunner implements InstallationRunnerInterface
{
    public function __construct(
        private ConfigWriterInterface $configWriter,
        private ShlinkAssetsHandlerInterface $assetsHandler,
        private ConfigGeneratorInterface $configGenerator,
    ) {
    }

    /** @inheritDoc */
    public function runInstallation(Command|null $initCommand, SymfonyStyle $io): int
    {
        return $this->run($initCommand, $io, isUpdate: false, importedConfig: ImportedConfig::notImported());
    }

    /** @inheritDoc */
    public function runUpdate(Command|null $initCommand, SymfonyStyle $io): int
    {
        $importConfig = $this->assetsHandler->resolvePreviousConfig($io);
        return $this->run($initCommand, $io, isUpdate: true, importedConfig: $importConfig);
    }

    /**
     * @return Command::SUCCESS|Command::FAILURE
     */
    private function run(
        Command|null $initCommand,
        SymfonyStyle $io,
        bool $isUpdate,
        ImportedConfig $importedConfig,
    ): int {
        $io->text([
            '<info>Welcome to Shlink!!</info>',
            'This tool will guide you through the installation process.',
        ]);

        // Check if a cached config file exists and drop it if so
        $this->assetsHandler->dropCachedConfigIfAny($io);

        if ($isUpdate) {
            $this->assetsHandler->importShlinkAssetsFromPath($io, $importedConfig->importPath);
        }
        $config = $this->configGenerator->generateConfigInteractively($io, $importedConfig->importedConfig);
        $normalizedConfig = Utils::normalizeAndKeepEnvVarKeys($config);

        // Generate config params files
        $this->configWriter->toFile(ShlinkAssetsHandler::GENERATED_CONFIG_PATH, $normalizedConfig);
        $io->text('<info>Custom configuration properly generated!</info>');
        $io->newLine();

        if (! $this->execInitCommand($initCommand, $io, $isUpdate, $importedConfig)) {
            return Command::FAILURE;
        }

        $io->success('Installation complete!');
        return Command::SUCCESS;
    }

    private function execInitCommand(
        Command|null $initCommand,
        SymfonyStyle $io,
        bool $isUpdate,
        ImportedConfig $importedConfig
    ): bool {
        $input = [
            InitOption::SKIP_INITIALIZE_DB->asCliFlag() => $isUpdate,
            InitOption::CLEAR_DB_CACHE->asCliFlag() => $isUpdate,
            InitOption::DOWNLOAD_RR_BINARY->asCliFlag() =>
                $isUpdate && $this->assetsHandler->roadRunnerBinaryExistsInPath($importedConfig->importPath),
        ];

        if (! $isUpdate) {
            $input[InitOption::INITIAL_API_KEY->asCliFlag()] = null;
        }

        return $initCommand?->run(new ArrayInput($input), $io) === Command::SUCCESS;
    }
}
