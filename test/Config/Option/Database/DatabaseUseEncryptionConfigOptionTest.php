<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Database;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Database\DatabaseUseEncryptionConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class DatabaseUseEncryptionConfigOptionTest extends TestCase
{
    private DatabaseUseEncryptionConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new DatabaseUseEncryptionConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('DB_USE_ENCRYPTION', $this->configOption->getEnvVar());
    }

    #[Test]
    #[TestWith([true])]
    #[TestWith([false])]
    public function expectedQuestionIsAsked(bool $expectedAnswer): void
    {
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('confirm')->with(
            'Do you want the database connection to be encrypted? Enabling this will make database connections fail if '
            . 'your database server does not support or enforce encryption.',
            false,
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }
}
