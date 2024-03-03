<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Server;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Server\RuntimeConfigOption;
use Shlinkio\Shlink\Installer\Config\Util\RuntimeType;
use Symfony\Component\Console\Style\StyleInterface;

class RuntimeConfigOptionTest extends TestCase
{
    private RuntimeConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new RuntimeConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('RUNTIME', $this->configOption->getEnvVar());
    }

    #[Test, DataProvider('provideRuntimes')]
    public function expectedQuestionIsAsked(string $answer, RuntimeType $expectedRuntime): void
    {
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('choice')->with(
            'Select the runtime you are planning to use to serve Shlink (this is only used to conditionally skip some '
            . 'follow-up questions)',
            [
                'RoadRunner',
                'Classic web server (Nginx, Apache, etc)',
            ],
            'RoadRunner',
        )->willReturn($answer);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedRuntime->value, $answer);
    }

    public static function provideRuntimes(): iterable
    {
        yield 'RoadRunner' => ['RoadRunner', RuntimeType::ASYNC];
        yield 'Classic web server' => ['Classic web server (Nginx, Apache, etc)', RuntimeType::REGULAR];
    }
}
