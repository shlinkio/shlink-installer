<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Factory;

use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Factory\ApplicationFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;

use function array_filter;
use function Functional\contains;

use const ARRAY_FILTER_USE_KEY;

class ApplicationFactoryTest extends TestCase
{
    private ApplicationFactory $factory;

    public function setUp(): void
    {
        $this->factory = new ApplicationFactory();
    }

    #[Test]
    public function serviceIsCreated(): void
    {
        $createEnabledCommandWithName = function (string $name) {
            $command = $this->createMock(Command::class);
            $command->method('isEnabled')->willReturn(true);
            $command->method('getAliases')->willReturn([]);
            $command->method('getName')->willReturn($name);
            $command->method('getDefinition')->willReturn(new InputDefinition());

            return $command;
        };

        $app = ($this->factory)(new ServiceManager(['services' => [
            'config' => [
                'installer' => [
                    'commands' => [
                        'install' => 'foo',
                        'update' => 'bar',
                    ],
                ],
            ],
            'foo' => $createEnabledCommandWithName('install'),
            'bar' => $createEnabledCommandWithName('update'),
        ]]), '');

        /** @var Command[] $commands */
        $commands = array_filter(
            $app->all(),
            // Remove standard symfony commands
            static fn (string $key) => ! contains(['list', 'help', 'completion', '_complete'], $key),
            ARRAY_FILTER_USE_KEY,
        );

        self::assertCount(2, $commands);
        self::assertTrue($app->has('install'));
        self::assertTrue($app->has('update'));
        self::assertFalse($app->has('invalid'));
    }
}
