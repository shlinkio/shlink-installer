<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Service;

use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

use function array_filter;
use function explode;
use function implode;
use function sprintf;
use function trim;

class InstallationCommandsRunner implements InstallationCommandsRunnerInterface
{
    private string $phpBinary;

    public function __construct(
        private ProcessHelper $processHelper,
        PhpExecutableFinder $phpFinder,
        private array $commandsMapping,
    ) {
        $this->phpBinary = $phpFinder->find(false) ?: 'php';
    }

    public function execPhpCommand(string $name, SymfonyStyle $io): bool
    {
        $commandConfig = $this->commandsMapping[$name] ?? null;
        if ($commandConfig === null) {
            return false;
        }

        [
            'command' => $command,
            'initMessage' => $initMessage,
            'errorMessage' => $errorMessage,
            'failOnError' => $failOnError,
            'printOutput' => $printOutput,
        ] = $commandConfig;
        $io->write($initMessage);

        // Skip the command if it's null, allowing it to be disabled
        if ($command === null) {
            $io->writeln(' <comment>Skipped</comment>');
            return true;
        }

        $command = [$this->phpBinary, ...$this->commandToArray($command)];
        $io->write(
            sprintf(' <options=bold>[Running "%s"]</> ', implode(' ', $command)),
            false,
            OutputInterface::VERBOSITY_VERBOSE,
        );

        $process = $this->processHelper->run($io, new Process($command, timeout: $commandConfig['timeout'] ?? 60));
        $isSuccess = $process->isSuccessful();
        $isWarning = ! $isSuccess && ! $failOnError;
        $isVerbose = $io->isVerbose();

        if ($isSuccess) {
            $io->writeln(' <info>Success!</info>');
        } elseif ($isWarning) {
            $io->write(' <comment>Warning!</comment>');
            $io->writeln($isVerbose ? '' : ' Run with -vvv to see error.');
        } elseif (! $isVerbose) {
            $io->error(sprintf('%s. Run this command with -vvv to see specific error info.', $errorMessage));
        }

        if ($printOutput) {
            $io->text($process->getOutput());
        }

        return $isSuccess || $isWarning;
    }

    private function commandToArray(string $command): array
    {
        $splitBySpace = explode(' ', trim($command));
        // array_filter ensures empty entries are removed, in case the command has duplicated spaces
        return array_filter($splitBySpace);
    }
}
