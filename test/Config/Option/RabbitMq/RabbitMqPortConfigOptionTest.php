<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\RabbitMq;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\RabbitMq\RabbitMqPortConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\RabbitMq\RabbitMqUseSslConfigOption;
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

    #[Test, DataProvider('provideCurrentOptions')]
    public function expectedQuestionIsAsked(array $currentOptions, string $expectedDefaultPort): void
    {
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('ask')->with(
            'RabbitMQ port',
            $expectedDefaultPort,
            $this->anything(),
        )->willReturn('5672');

        $answer = $this->configOption->ask($io, $currentOptions);

        self::assertEquals(5672, $answer);
    }

    public static function provideCurrentOptions(): iterable
    {
        yield 'no config' => [[], '5672'];
        yield 'no ssl' => [[RabbitMqUseSslConfigOption::ENV_VAR => false], '5672'];
        yield 'ssl' => [[RabbitMqUseSslConfigOption::ENV_VAR => true], '5671'];
    }

    #[Test]
    public function dependsOnDriver(): void
    {
        self::assertEquals(RabbitMqUseSslConfigOption::class, $this->configOption->getDependentOption());
    }
}
