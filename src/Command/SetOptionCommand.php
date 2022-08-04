<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Command;

use Generator;
use Laminas\Config\Writer\WriterInterface;
use Shlinkio\Shlink\Installer\Config\ConfigOptionsManagerInterface;
use Shlinkio\Shlink\Installer\Config\Option\ConfigOptionInterface;
use Shlinkio\Shlink\Installer\Exception\InvalidShlinkPathException;
use Shlinkio\Shlink\Installer\Service\ShlinkAssetsHandler;
use Shlinkio\Shlink\Installer\Service\ShlinkAssetsHandlerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

use function array_filter;
use function array_keys;
use function Functional\contains;
use function getcwd;
use function is_iterable;
use function is_numeric;
use function iterator_to_array;

class SetOptionCommand extends Command
{
    public const NAME = 'set-option';

    private array $groups;
    private string $generatedConfigPath;

    public function __construct(
        private WriterInterface $configWriter,
        private ShlinkAssetsHandlerInterface $assetsHandler,
        private ConfigOptionsManagerInterface $optionsManager,
        private Filesystem $filesystem,
        array $groups,
        ?array $enabledOptions,
    ) {
        parent::__construct();
        $this->groups = array_filter(
            iterator_to_array($this->flattenGroupsWithTitle($groups)),
            static fn (string $configOption) => $enabledOptions === null || contains($enabledOptions, $configOption),
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

    protected function configure(): void
    {
        $this
            ->setName(self::NAME)
            ->setDescription('Allows you to set new values for any config option.');
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        if (! $this->filesystem->exists($this->generatedConfigPath)) {
            throw InvalidShlinkPathException::forCurrentPath();
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $io = new SymfonyStyle($input, $output);
        $optionTitle = $io->choice('What config option do you want to change', array_keys($this->groups));

        /** @var ConfigOptionInterface $plugin */
        $plugin = $this->optionsManager->get($this->groups[$optionTitle]);
        $answers = include $this->generatedConfigPath;
        $answers[$plugin->getEnvVar()] = $plugin->ask($io, $answers);
        $this->configWriter->toFile($this->generatedConfigPath, $answers, false);
        $this->assetsHandler->dropCachedConfigIfAny($io);

        $io->success('Configuration properly updated');

        return self::SUCCESS;
    }
}
