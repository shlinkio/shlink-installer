<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Server;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Server\MemoryLimitConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class MemoryLimitConfigOptionTest extends TestCase
{
    private MemoryLimitConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new MemoryLimitConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('MEMORY_LIMIT', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('ask')->with(
            'What is the maximum amount of RAM every process run by Shlink should be allowed to use? (Provide a '
            . 'number for bytes, a number followed by K for kilobytes, M for Megabytes or G for Gigabytes)',
            '512M',
            $this->anything(),
        )->willReturn('1G');

        $answer = $this->configOption->ask($io, []);

        self::assertEquals('1G', $answer);
    }
}
