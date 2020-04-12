<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Factory;

use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Installer\Config;
use Shlinkio\Shlink\Installer\Factory\InstallApplicationFactory;
use Shlinkio\Shlink\Installer\Service;
use Symfony\Component\Console\Command\Command;

use function array_filter;
use function array_shift;
use function Functional\contains;

use const ARRAY_FILTER_USE_KEY;

class InstallApplicationFactoryTest extends TestCase
{
    use ProphecyTrait;

    private InstallApplicationFactory $factory;

    public function setUp(): void
    {
        $this->factory = new InstallApplicationFactory();
    }

    /**
     * @test
     */
    public function serviceIsCreated(): void
    {
        $app = ($this->factory)(new ServiceManager(['services' => [
            Service\ShlinkAssetsHandler::class => $this->prophesize(
                Service\ShlinkAssetsHandlerInterface::class,
            )->reveal(),
            Service\InstallationCommandsRunner::class => $this->prophesize(
                Service\InstallationCommandsRunnerInterface::class,
            )->reveal(),
            Config\ConfigGenerator::class => $this->prophesize(Config\ConfigGeneratorInterface::class)->reveal(),
        ]]), '');

        /** @var Command[] $commands */
        $commands = array_filter(
            $app->all(),
            fn (string $key) => ! contains(['list', 'help'], $key), // Remove list and help commands
            ARRAY_FILTER_USE_KEY,
        );

        $this->assertCount(1, $commands);
        $this->assertEquals('shlink:install', array_shift($commands)->getName());
    }
}
