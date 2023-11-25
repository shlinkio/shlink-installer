<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\RabbitMq;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\RabbitMq\RabbitMqPortConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class RabbitMqPortConfigOptionTest extends TestCase
{
    private RabbitMqPortConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new RabbitMqPortConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('RABBITMQ_PORT', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('ask')->with('RabbitMQ port', '5672', $this->anything())->willReturn(
            '5672',
        );

        $answer = $this->configOption->ask($io, []);

        self::assertEquals(5672, $answer);
    }
}
