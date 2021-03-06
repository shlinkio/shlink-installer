<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Factory;

use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Installer\Factory\ApplicationFactory;
use Symfony\Component\Console\Command\Command;

use function array_filter;
use function Functional\contains;

use const ARRAY_FILTER_USE_KEY;

class ApplicationFactoryTest extends TestCase
{
    use ProphecyTrait;

    private ApplicationFactory $factory;

    public function setUp(): void
    {
        $this->factory = new ApplicationFactory();
    }

    /**
     * @test
     */
    public function serviceIsCreated(): void
    {
        $createEnabledCommandWithName = function (string $name) {
            $command = $this->prophesize(Command::class);
            $command->isEnabled()->willReturn(true);
            $command->getAliases()->willReturn([]);
            $command->getName()->willReturn($name);
            $command->setApplication(Argument::any())->will(function (): void {
            });
            $command->getDefinition()->will(function (): void {
            });

            return $command->reveal();
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
            static fn (string $key) => ! contains(['list', 'help'], $key), // Remove list and help commands
            ARRAY_FILTER_USE_KEY,
        );

        self::assertCount(2, $commands);
        self::assertTrue($app->has('install'));
        self::assertTrue($app->has('update'));
        self::assertFalse($app->has('invalid'));
    }
}
