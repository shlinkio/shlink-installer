<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Service;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
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
    private InstallationCommandsRunner $commandsRunner;
    private MockObject & ProcessHelper $processHelper;
    private MockObject & SymfonyStyle $io;

    public function setUp(): void
    {
        $phpFinder = $this->createMock(PhpExecutableFinder::class);
        $phpFinder->method('find')->with(false)->willReturn('php');

        $this->processHelper = $this->createMock(ProcessHelper::class);
        $this->commandsRunner = new InstallationCommandsRunner($this->processHelper, $phpFinder, $this->buildCommands(
            ['foo', 'bar', 'null_command'],
        ));

        $this->io = $this->createMock(SymfonyStyle::class);
        $this->io->method('isVerbose')->willReturn(false);
    }

    private function buildCommands(array $names): array
    {
        return array_combine($names, map($names, fn (string $name) => [
            'command' => $name === 'null_command' ? null : sprintf('%s something', $name),
            'initMessage' => sprintf('%s_init', $name),
            'errorMessage' => sprintf('%s_error', $name),
            'failOnError' => $name === 'foo',
            'printOutput' => false,
        ]));
    }

    /** @test */
    public function doesNothingWhenRequestedCommandDoesNotExist(): void
    {
        self::assertFalse($this->commandsRunner->execPhpCommand('invalid', $this->io));
    }

    /**
     * @test
     * @dataProvider provideCommandNames
     */
    public function returnsSuccessWhenProcessIsProperlyRunOrDoesNotFailOnError(string $name): void
    {
        $command = ['php', $name, 'something'];

        $process = $this->createProcessMock($name === 'foo');
        $this->processHelper->expects($this->once())->method('run')->with($this->io, $command)->willReturn($process);

        $callCount = 0;
        $this->io->expects($this->exactly(2))->method('write')->willReturnCallback(
            function (string $messages, bool $newline, int $type) use (&$callCount, $name, $command): void {
                if ($callCount === 0) {
                    Assert::assertEquals(sprintf('%s_init', $name), $messages);
                } elseif ($callCount === 1) {
                    Assert::assertStringContainsString(sprintf('Running "%s"', implode(' ', $command)), $messages);
                    Assert::assertFalse($newline);
                    Assert::assertEquals(OutputInterface::VERBOSITY_VERBOSE, $type);
                }
                $callCount++;
            },
        );
        $this->io->expects($this->once())->method('writeln')->with(' <info>Success!</info>', $this->anything());
        $this->io->expects($this->never())->method('error');

        self::assertTrue($this->commandsRunner->execPhpCommand($name, $this->io));
    }

    public static function provideCommandNames(): array
    {
        return [['foo'], ['bar']];
    }

    /** @test */
    public function returnsErrorWhenProcessIsNotProperlyRun(): void
    {
        $name = 'foo';
        $command = ['php', $name, 'something'];

        $process = $this->createProcessMock(false);
        $this->processHelper->expects($this->once())->method('run')->with($this->io, $command)->willReturn($process);

        $callCount = 0;
        $this->io->expects($this->exactly(2))->method('write')->willReturnCallback(
            function (string $messages, bool $newline, int $type) use (&$callCount, $name, $command): void {
                if ($callCount === 0) {
                    Assert::assertEquals(sprintf('%s_init', $name), $messages);
                } elseif ($callCount === 1) {
                    Assert::assertStringContainsString(sprintf('Running "%s"', implode(' ', $command)), $messages);
                    Assert::assertFalse($newline);
                    Assert::assertEquals(OutputInterface::VERBOSITY_VERBOSE, $type);
                }
                $callCount++;
            },
        );
        $this->io->expects($this->once())->method('error')->with($this->stringContains(sprintf('%s_error', $name)));
        $this->io->expects($this->never())->method('writeln');

        self::assertFalse($this->commandsRunner->execPhpCommand($name, $this->io));
    }

    /** @test */
    public function skipsNullCommands(): void
    {
        $name = 'null_command';

        $this->processHelper->expects($this->never())->method('run');
        $this->io->expects($this->once())->method('write')->with(sprintf('%s_init', $name), $this->anything());
        $this->io->expects($this->never())->method('error');
        $this->io->expects($this->once())->method('writeln')->with(' <comment>Skipped</comment>', $this->anything());

        self::assertTrue($this->commandsRunner->execPhpCommand($name, $this->io));
    }

    private function createProcessMock(bool $isSuccessful): MockObject & Process
    {
        $process = $this->createMock(Process::class);
        $process->method('isSuccessful')->willReturn($isSuccessful);

        return $process;
    }
}
