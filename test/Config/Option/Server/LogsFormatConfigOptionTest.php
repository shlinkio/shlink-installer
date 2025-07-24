<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Server;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Server\LogsFormatConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class LogsFormatConfigOptionTest extends TestCase
{
    private LogsFormatConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new LogsFormatConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('LOGS_FORMAT', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $io = $this->createMock(StyleInterface::class);
        $answer = 'json';
        $io->expects($this->once())->method('choice')->with(
            'In what format do you want Shlink to generate logs?',
            ['console', 'json'],
            'console',
        )->willReturn($answer);

        $result = $this->configOption->ask($io, []);

        self::assertEquals($answer, $result);
    }
}
