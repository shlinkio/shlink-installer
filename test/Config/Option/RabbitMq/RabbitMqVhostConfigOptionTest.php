<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\RabbitMq;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
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
    public function returnsExpectedConfig(): void
    {
        self::assertEquals(['rabbitmq', 'vhost'], $this->configOption->getConfigPath());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 'the_answer';
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask('RabbitMQ VHost', '/')->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io->reveal(), new PathCollection());

        self::assertEquals($expectedAnswer, $answer);
        $ask->shouldHaveBeenCalledOnce();
    }
}
