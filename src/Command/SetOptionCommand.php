<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Command;

use Generator;
use Shlinkio\Shlink\Installer\Config\ConfigOptionsManagerInterface;
use Shlinkio\Shlink\Installer\Config\Option\ConfigOptionInterface;
use Shlinkio\Shlink\Installer\Exception\InvalidShlinkPathException;
use Shlinkio\Shlink\Installer\Service\ShlinkAssetsHandler;
use Shlinkio\Shlink\Installer\Service\ShlinkAssetsHandlerInterface;
use Shlinkio\Shlink\Installer\Util\ArrayUtils;
use Shlinkio\Shlink\Installer\Util\ConfigWriterInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

use function array_filter;
use function array_keys;
use function getcwd;
use function is_iterable;
use function is_numeric;
use function iterator_to_array;

#[AsCommand(SetOptionCommand::NAME, 'Allows you to set new values for any config option')]
class SetOptionCommand extends Command
{
    public const string NAME = 'set-option';

    private array $groups;
    private string $generatedConfigPath;

    public function __construct(
        private readonly ConfigWriterInterface $configWriter,
        private readonly ShlinkAssetsHandlerInterface $assetsHandler,
        private readonly ConfigOptionsManagerInterface $optionsManager,
        private readonly Filesystem $filesystem,
        array $groups,
        array|null $enabledOptions,
    ) {
        parent::__construct();
        $this->groups = array_filter(
            iterator_to_array($this->flattenGroupsWithTitle($groups)),
            static fn (string $configOption) => $enabledOptions === null || ArrayUtils::contains(
                $configOption,
                $enabledOptions,
            ),
        );
        $this->generatedConfigPath = getcwd() . '/' . ShlinkAssetsHandler::GENERATED_CONFIG_PATH;
    }

    private function flattenGroupsWithTitle(iterable $groups): Generator
    {
        foreach ($groups as $key => $value) {
            if (is_iterable($value)) {
                yield from $this->flattenGroupsWithTitle($value);
            } elseif (! is_numeric($key)) {
                yield $key => $value;
            }
        }
    }

    public function __invoke(SymfonyStyle $io): int
    {
        if (! $this->filesystem->exists($this->generatedConfigPath)) {
            throw InvalidShlinkPathException::forCurrentPath();
        }

        $optionTitle = $io->choice('What config option do you want to change', array_keys($this->groups));

        /** @var ConfigOptionInterface $plugin */
        $plugin = $this->optionsManager->get($this->groups[$optionTitle]);
        $answers = include $this->generatedConfigPath;
        $answers[$plugin->getEnvVar()] = $plugin->ask($io, $answers);
        $this->configWriter->toFile($this->generatedConfigPath, $answers);
        $this->assetsHandler->dropCachedConfigIfAny($io);

        $io->success('Configuration properly updated');

        return self::SUCCESS;
    }
}
