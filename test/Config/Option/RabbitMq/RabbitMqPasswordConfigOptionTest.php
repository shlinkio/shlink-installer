<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\RabbitMq;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Installer\Config\Option\RabbitMq\RabbitMqPasswordConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class RabbitMqPasswordConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private RabbitMqPasswordConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new RabbitMqPasswordConfigOption(fn () => true);
    }

    /** @test */
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('RABBITMQ_PASSWORD', $this->configOption->getEnvVar());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 'the_answer';
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('ask')->with('RabbitMQ password', $this->anything())->willReturn(
            $expectedAnswer,
        );

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }
}
