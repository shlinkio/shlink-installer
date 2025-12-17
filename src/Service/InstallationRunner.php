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
    public function runInstallation(SymfonyStyle $io, Command|null $initCommand): int
    {
        $initCommandInput = [InitOption::INITIAL_API_KEY->asCliFlag() => null];
        return $this->run($io, $initCommand, $initCommandInput, ImportedConfig::notImported());
    }

    /** @inheritDoc */
    public function runUpdate(SymfonyStyle $io, Command|null $initCommand): int
    {
        $importConfig = $this->assetsHandler->resolvePreviousConfig($io);

        // Check if a cached config file exists and drop it if so
        $this->assetsHandler->dropCachedConfigIfAny($io);
        $this->assetsHandler->importShlinkAssetsFromPath($io, $importConfig->importPath);

        $initCommandInput = [
            InitOption::SKIP_INITIALIZE_DB->asCliFlag() => null,
            InitOption::CLEAR_DB_CACHE->asCliFlag() => null,
        ];

        if ($this->assetsHandler->roadRunnerBinaryExistsInPath($importConfig->importPath)) {
            $initCommandInput[InitOption::DOWNLOAD_RR_BINARY->asCliFlag()] = null;
        }

        return $this->run($io, $initCommand, $initCommandInput, $importConfig);
    }

    /**
     * @return Command::SUCCESS|Command::FAILURE
     */
    private function run(
        SymfonyStyle $io,
        Command|null $initCommand,
        array $initCommandInput,
        ImportedConfig $importedConfig,
    ): int {
        $io->text([
            '<info>Welcome to Shlink!!</info>',
            'This tool will guide you through the installation process.',
        ]);

        $config = $this->configGenerator->generateConfigInteractively($io, $importedConfig->importedConfig);
        $normalizedConfig = Utils::normalizeAndKeepEnvVarKeys($config);

        // Generate config params files
        $this->configWriter->toFile(ShlinkAssetsHandler::GENERATED_CONFIG_PATH, $normalizedConfig);
        $io->text('<info>Custom configuration properly generated!</info>');
        $io->newLine();

        $initCommandResult = $initCommand?->run(new ArrayInput($initCommandInput), $io);
        if ($initCommandResult !== Command::SUCCESS) {
            return Command::FAILURE;
        }

        $io->success('Installation complete!');
        return Command::SUCCESS;
    }
}
