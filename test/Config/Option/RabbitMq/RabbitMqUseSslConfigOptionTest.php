<?php

declare(strict_types=1);

namespace Config\Option\RabbitMq;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\RabbitMq\RabbitMqUseSslConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class RabbitMqUseSslConfigOptionTest extends TestCase
{
    private RabbitMqUseSslConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new RabbitMqUseSslConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('RABBITMQ_USE_SSL', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('confirm')->with('Should use SSL?', false)->willReturn(true);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals(true, $answer);
    }
}
