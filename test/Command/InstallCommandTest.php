<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Command;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Command\InstallCommand;
use Shlinkio\Shlink\Installer\Service\InstallationRunnerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class InstallCommandTest extends TestCase
{
    private CommandTester $commandTester;
    private MockObject & InstallationRunnerInterface $installationRunner;

    public function setUp(): void
    {
        $this->installationRunner = $this->createMock(InstallationRunnerInterface::class);

        $command = new InstallCommand($this->installationRunner);
        $this->commandTester = new CommandTester($command);
    }

    #[Test]
    #[TestWith([Command::SUCCESS])]
    #[TestWith([Command::FAILURE])]
    public function commandIsExecutedAsExpected(int $statusCode): void
    {
        $this->installationRunner->expects($this->once())->method('runInstallation')->willReturn($statusCode);
        $this->commandTester->execute([]);

        self::assertEquals($statusCode, $this->commandTester->getStatusCode());
    }
}
