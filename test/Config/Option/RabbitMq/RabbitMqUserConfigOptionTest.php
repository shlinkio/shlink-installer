<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\RabbitMq;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\RabbitMq\RabbitMqUserConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class RabbitMqUserConfigOptionTest extends TestCase
{
    private RabbitMqUserConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new RabbitMqUserConfigOption(fn () => true);
    }

    /** @test */
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('RABBITMQ_USER', $this->configOption->getEnvVar());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 'the_answer';
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('ask')->with('RabbitMQ username', $this->anything())->willReturn(
            $expectedAnswer,
        );

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }
}
