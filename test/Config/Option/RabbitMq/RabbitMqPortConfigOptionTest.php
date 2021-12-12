<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\RabbitMq;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\RabbitMq\RabbitMqPortConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class RabbitMqPortConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private RabbitMqPortConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new RabbitMqPortConfigOption(fn () => true);
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        self::assertEquals(['rabbitmq', 'port'], $this->configOption->getConfigPath());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask('RabbitMQ port', '5672', Argument::any())->willReturn('5672');

        $answer = $this->configOption->ask($io->reveal(), new PathCollection());

        self::assertEquals(5672, $answer);
        $ask->shouldHaveBeenCalledOnce();
    }
}
