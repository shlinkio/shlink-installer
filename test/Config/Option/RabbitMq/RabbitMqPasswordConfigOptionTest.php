<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\RabbitMq;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
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
    public function returnsExpectedConfig(): void
    {
        self::assertEquals(['rabbitmq', 'password'], $this->configOption->getConfigPath());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 'the_answer';
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask('RabbitMQ password', Argument::cetera())->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io->reveal(), new PathCollection());

        self::assertEquals($expectedAnswer, $answer);
        $ask->shouldHaveBeenCalledOnce();
    }
}
