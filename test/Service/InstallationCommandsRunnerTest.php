<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Service;

use InvalidArgumentException;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Service\InstallationCommandsRunner;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

use function array_combine;
use function Functional\map;
use function implode;
use function sprintf;

class InstallationCommandsRunnerTest extends TestCase
{
    private InstallationCommandsRunner $commandsRunner;
    private MockObject & ProcessHelper $processHelper;
    private MockObject & SymfonyStyle $io;

    public function setUp(): void
    {
        $phpFinder = $this->createMock(PhpExecutableFinder::class);
        $phpFinder->method('find')->with(false)->willReturn('php');

        $this->processHelper = $this->createMock(ProcessHelper::class);
        $this->commandsRunner = new InstallationCommandsRunner(
            $this->processHelper,
            $phpFinder,
            $this->buildCommands(),
        );

        $this->io = $this->createMock(SymfonyStyle::class);
    }

    private function buildCommands(): array
    {
        $names = ['foo', 'bar', 'null_command', 'multiple  spaces   '];
        return array_combine($names, map($names, fn (string $name) => [
            'command' => $name === 'null_command' ? null : sprintf('%s something', $name),
            'initMessage' => sprintf('%s_init', $name),
            'errorMessage' => sprintf('%s_error', $name),
            'failOnError' => $name === 'foo',
            'printOutput' => false,
            'timeout' => $name === 'foo' ? 1000 : null,
        ]));
    }

    #[Test]
    public function doesNothingWhenRequestedCommandDoesNotExist(): void
    {
        self::assertFalse($this->commandsRunner->execPhpCommand('invalid', $this->io, interactive: true, args: []));
    }

    #[Test, DataProvider('provideTimeouts')]
    public function returnsSuccessWhenProcessIsProperlyRun(string $name, float $expectedTimeout): void
    {
        $command = ['php', $name, 'something'];

        $process = $this->createProcessMock(true);
        $this->processHelper->expects($this->once())->method('run')->with(
            $this->io,
            $this->callback(fn (Process $process) => $expectedTimeout === $process->getTimeout()),
        )->willReturn($process);

        $writeCallMatcher = $this->exactly(2);
        $this->io->expects($writeCallMatcher)->method('write')->willReturnCallback(
            function (string $message) use ($writeCallMatcher, $name, $command): void {
                match ($writeCallMatcher->numberOfInvocations()) {
                    1 => Assert::assertEquals(sprintf('%s_init', $name), $message),
                    2 => Assert::assertStringContainsString(sprintf('Running "%s"', implode(' ', $command)), $message),
                    default => throw new InvalidArgumentException('Not valid case'),
                };
            },
        );
        $this->io->expects($this->once())->method('writeln')->with(' <info>Success!</info>', $this->anything());
        $this->io->expects($this->never())->method('error');

        self::assertTrue($this->commandsRunner->execPhpCommand($name, $this->io, interactive: true, args: []));
    }

    public static function provideTimeouts(): iterable
    {
        yield 'default timeout' => ['bar', 60];
        yield 'explicit timeout' => ['foo', 1000];
    }

    #[Test, DataProvider('provideExtraLines')]
    public function returnsWarningWhenProcessFailsButErrorIsAllowed(
        bool $isVerbose,
        bool $isInteractive,
        string $extraLine,
    ): void {
        $name = 'bar';
        $command = ['php', $name, 'something'];

        $process = $this->createProcessMock(false);
        $this->processHelper->expects($this->once())->method('run')->with(
            $this->io,
            $this->isInstanceOf(Process::class),
        )->willReturn($process);
        $this->io->method('isVerbose')->willReturn($isVerbose);

        $writeCallMatcher = $this->exactly(3);
        $this->io->expects($writeCallMatcher)->method('write')->willReturnCallback(
            function (string $message) use ($writeCallMatcher, $name, $command): void {
                match ($writeCallMatcher->numberOfInvocations()) {
                    1 => Assert::assertEquals(sprintf('%s_init', $name), $message),
                    2 => Assert::assertStringContainsString(sprintf('Running "%s"', implode(' ', $command)), $message),
                    3 => Assert::assertEquals(' <comment>Warning!</comment>', $message),
                    default => throw new InvalidArgumentException('Not valid case'),
                };
            },
        );
        $this->io->expects($this->once())->method('writeln')->with($extraLine);
        $this->io->expects($this->never())->method('error');

        self::assertTrue(
            $this->commandsRunner->execPhpCommand($name, $this->io, interactive: $isInteractive, args: []),
        );
    }

    public static function provideExtraLines(): iterable
    {
        yield 'interactive, verbose output' => [true, true, ''];
        yield 'interactive, not verbose output' => [false, true, ' Run with -vvv to see error.'];
        yield 'non-interactive, verbose output' => [true, false, ''];
        yield 'non-interactive, not verbose output' => [false, false, ' Set SHELL_VERBOSITY=3 to see error.'];
    }

    #[Test, DataProvider('provideInteractivityFlag')]
    public function returnsErrorWhenProcessIsNotProperlyRun(
        bool $isInteractive,
        string $expectedError,
        string $notExpectedError,
    ): void {
        $name = 'foo';
        $command = ['php', $name, 'something'];

        $process = $this->createProcessMock(false);
        $this->processHelper->expects($this->once())->method('run')->with(
            $this->io,
            $this->isInstanceOf(Process::class),
        )->willReturn($process);

        $writeCallMatcher = $this->exactly(2);
        $this->io->expects($writeCallMatcher)->method('write')->willReturnCallback(
            function (string $message) use ($writeCallMatcher, $name, $command): void {
                match ($writeCallMatcher->numberOfInvocations()) {
                    1 => Assert::assertEquals(sprintf('%s_init', $name), $message),
                    2 => Assert::assertStringContainsString(sprintf('Running "%s"', implode(' ', $command)), $message),
                    default => throw new InvalidArgumentException('Not valid case'),
                };
            },
        );
        $this->io->expects($this->once())->method('error')->with($this->logicalAnd(
            $this->stringContains(sprintf('%s_error', $name)),
            $this->stringContains($expectedError),
            $this->logicalNot($this->stringContains($notExpectedError)),
        ));
        $this->io->expects($this->never())->method('writeln');

        self::assertFalse(
            $this->commandsRunner->execPhpCommand($name, $this->io, interactive: $isInteractive, args: []),
        );
    }

    public static function provideInteractivityFlag(): iterable
    {
        $verbosityFlagMessage = 'Run with -vvv';
        $envVarMessage = 'Set SHELL_VERBOSITY=3';

        yield 'interactive' => [true, $verbosityFlagMessage, $envVarMessage];
        yield 'non-interactive' => [false, $envVarMessage, $verbosityFlagMessage];
    }

    #[Test]
    public function skipsNullCommands(): void
    {
        $name = 'null_command';

        $this->processHelper->expects($this->never())->method('run');
        $this->io->expects($this->once())->method('write')->with(sprintf('%s_init', $name), $this->anything());
        $this->io->expects($this->never())->method('error');
        $this->io->expects($this->once())->method('writeln')->with(' <comment>Skipped</comment>', $this->anything());

        self::assertTrue($this->commandsRunner->execPhpCommand($name, $this->io, interactive: true, args: []));
    }

    #[Test]
    public function appendsProviedArgumentsToCommand(): void
    {
    }

    private function createProcessMock(bool $isSuccessful): MockObject & Process
    {
        $process = $this->createMock(Process::class);
        $process->method('isSuccessful')->willReturn($isSuccessful);

        return $process;
    }
}
