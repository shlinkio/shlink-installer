<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\RabbitMq;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\RabbitMq\RabbitMqHostConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class RabbitMqHostConfigOptionTest extends TestCase
{
    private RabbitMqHostConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new RabbitMqHostConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('RABBITMQ_HOST', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 'the_answer';
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('ask')->with('RabbitMQ host name', $this->anything())->willReturn(
            $expectedAnswer,
        );

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }
}
