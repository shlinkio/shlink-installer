<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Service;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Shlinkio\Shlink\Installer\Service\InstallationCommandsRunner;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

use function array_combine;
use function Functional\map;
use function implode;
use function sprintf;

class InstallationCommandsRunnerTest extends TestCase
{
    use ProphecyTrait;

    private const COMMAND_NAMES = ['foo', 'bar'];

    private InstallationCommandsRunner $commandsRunner;
    private ObjectProphecy $processHelper;
    private ObjectProphecy $io;

    public function setUp(): void
    {
        $phpFinder = $this->prophesize(PhpExecutableFinder::class);
        $phpFinder->find(false)->willReturn('php');

        $this->processHelper = $this->prophesize(ProcessHelper::class);
        $this->commandsRunner = new InstallationCommandsRunner(
            $this->processHelper->reveal(),
            $phpFinder->reveal(),
            $this->buildCommands(self::COMMAND_NAMES),
        );

        $this->io = $this->prophesize(SymfonyStyle::class);
    }

    private function buildCommands(array $names): array
    {
        return array_combine($names, map($names, fn (string $name) => [
            'command' => sprintf('%s something', $name),
            'initMessage' => sprintf('%s_init', $name),
            'errorMessage' => sprintf('%s_error', $name),
            'failOnError' => $name === 'foo',
        ]));
    }

    /** @test */
    public function doesNothingWhenRequestedCommandDoesNotExist(): void
    {
        self::assertFalse($this->commandsRunner->execPhpCommand('invalid', $this->io->reveal()));
    }

    /**
     * @test
     * @dataProvider provideCommandNames
     */
    public function returnsSuccessWhenProcessIsProperlyRunOrDoesNotFailOnError(string $name): void
    {
        $command = ['php', $name, 'something'];

        $process = $this->createProcessMock($name === 'foo');
        $run = $this->processHelper->run($this->io->reveal(), $command)->willReturn(
            $process->reveal(),
        );

        $writInitMsg = $this->io->write(sprintf('%s_init', $name));
        $writRunningMsg = $this->io->write(
            Argument::containingString(sprintf('Running "%s"', implode(' ', $command))),
            false,
            OutputInterface::VERBOSITY_VERBOSE,
        );
        $writErrorMsg = $this->io->error(Argument::containingString(sprintf('%s_error', $name)));
        $writSuccessMsg = $this->io->writeln(' <info>Success!</info>');

        self::assertTrue($this->commandsRunner->execPhpCommand($name, $this->io->reveal()));
        $run->shouldHaveBeenCalledOnce();
        $writInitMsg->shouldHaveBeenCalledOnce();
        $writRunningMsg->shouldHaveBeenCalledOnce();
        $writErrorMsg->shouldNotHaveBeenCalled();
        $writSuccessMsg->shouldHaveBeenCalledOnce();
    }

    public function provideCommandNames(): array
    {
        return map(self::COMMAND_NAMES, fn (string $name) => [$name]);
    }

    /** @test */
    public function returnsErrorWhenProcessIsNotProperlyRun(): void
    {
        $name = 'foo';
        $command = ['php', $name, 'something'];

        $process = $this->createProcessMock(false);
        $run = $this->processHelper->run($this->io->reveal(), $command)->willReturn(
            $process->reveal(),
        );

        $writInitMsg = $this->io->write(sprintf('%s_init', $name));
        $writRunningMsg = $this->io->write(
            Argument::containingString(sprintf('Running "%s"', implode(' ', $command))),
            false,
            OutputInterface::VERBOSITY_VERBOSE,
        );
        $writErrorMsg = $this->io->error(Argument::containingString(sprintf('%s_error', $name)));
        $writSuccessMsg = $this->io->writeln(' <info>Success!</info>');

        self::assertFalse($this->commandsRunner->execPhpCommand($name, $this->io->reveal()));
        $run->shouldHaveBeenCalledOnce();
        $writInitMsg->shouldHaveBeenCalledOnce();
        $writRunningMsg->shouldHaveBeenCalledOnce();
        $writErrorMsg->shouldHaveBeenCalledOnce();
        $writSuccessMsg->shouldNotHaveBeenCalled();
    }

    private function createProcessMock(bool $isSuccessful): ObjectProphecy
    {
        $process = $this->prophesize(Process::class);
        $process->isSuccessful()->willReturn($isSuccessful);

        return $process;
    }
}
