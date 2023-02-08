<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Database;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Database\DatabaseUserConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class DatabaseUserConfigOptionTest extends TestCase
{
    private DatabaseUserConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new DatabaseUserConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('DB_USER', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 'the_answer';
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('ask')->with('Database username', null, $this->anything())->willReturn(
            $expectedAnswer,
        );

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }
}
