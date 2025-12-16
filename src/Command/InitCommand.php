<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Command;

use Shlinkio\Shlink\Installer\Command\Model\InitCommandInput;
use Shlinkio\Shlink\Installer\Service\InstallationCommandsRunnerInterface;
use Shlinkio\Shlink\Installer\Util\InstallationCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\MapInput;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function array_reduce;

#[AsCommand(
    name: InitCommand::NAME,
    description: 'Initializes external dependencies required for Shlink to properly work, like DB, cache warmup, '
        . 'initial GeoLite DB download, etc',
)]
class InitCommand extends Command
{
    public const string NAME = 'init';

    public function __construct(private readonly InstallationCommandsRunnerInterface $commandsRunner)
    {
        parent::__construct();
    }

    public function __invoke(SymfonyStyle $io, InputInterface $input, #[MapInput] InitCommandInput $inputData): int
    {
        $commands = [...$inputData->resolveCommands()];

        return array_reduce($commands, function (bool $carry, array $commandInfo) use ($input, $io): bool {
            /** @var array{InstallationCommand, string|null} $commandInfo */
            [$command, $arg] = $commandInfo;

            return $this->commandsRunner->execPhpCommand(
                name: $command->value,
                io: $io,
                interactive: $input->isInteractive(),
                args: $arg !== null ? [$arg] : [],
            ) && $carry;
        }, initial: true) ? self::SUCCESS : self::FAILURE;
    }
}
