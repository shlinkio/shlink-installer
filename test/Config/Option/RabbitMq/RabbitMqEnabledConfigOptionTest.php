<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\RabbitMq;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\RabbitMq\RabbitMqEnabledConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class RabbitMqEnabledConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private RabbitMqEnabledConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new RabbitMqEnabledConfigOption(fn () => true);
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        self::assertEquals(['rabbitmq', 'enabled'], $this->configOption->getDeprecatedPath());
        self::assertEquals('RABBITMQ_ENABLED', $this->configOption->getEnvVar());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $io = $this->prophesize(StyleInterface::class);
        $confirm = $io->confirm(
            'Do you want Shlink to publish real-time updates in a RabbitMQ instance?',
            false,
        )->willReturn(true);

        $answer = $this->configOption->ask($io->reveal(), new PathCollection());

        self::assertEquals(true, $answer);
        $confirm->shouldHaveBeenCalledOnce();
    }
}
