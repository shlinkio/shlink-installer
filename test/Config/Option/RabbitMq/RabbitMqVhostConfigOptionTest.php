<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\RabbitMq;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Installer\Config\Option\RabbitMq\RabbitMqVhostConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class RabbitMqVhostConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private RabbitMqVhostConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new RabbitMqVhostConfigOption(fn () => true);
    }

    /** @test */
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('RABBITMQ_VHOST', $this->configOption->getEnvVar());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 'the_answer';
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask('RabbitMQ VHost', '/')->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io->reveal(), []);

        self::assertEquals($expectedAnswer, $answer);
        $ask->shouldHaveBeenCalledOnce();
    }
}
