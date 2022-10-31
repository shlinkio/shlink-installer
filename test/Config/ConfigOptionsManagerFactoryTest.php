<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use ReflectionObject;
use Shlinkio\Shlink\Installer\Config\ConfigOptionsManagerFactory;
use Shlinkio\Shlink\Installer\Config\Option\ConfigOptionInterface;

class ConfigOptionsManagerFactoryTest extends TestCase
{
    private ConfigOptionsManagerFactory $factory;
    private MockObject & ContainerInterface $container;

    public function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->factory = new ConfigOptionsManagerFactory();
    }

    /**
     * @test
     * @dataProvider provideConfigs
     */
    public function createsServiceWithExpectedPlugins(array $config, int $expectedSize): void
    {
        $this->container->expects($this->once())->method('get')->with('config')->willReturn($config);

        $service = ($this->factory)($this->container);
        $ref = new ReflectionObject($service);
        $servicesProp = $ref->getProperty('services');
        $servicesProp->setAccessible(true);

        self::assertCount($expectedSize, $servicesProp->getValue($service));
    }

    public function provideConfigs(): iterable
    {
        yield 'config_options not defined' => [[], 0];
        yield 'config_options empty' => [['config_options' => []], 0];
        yield 'config_options with values' => [
            [
                'config_options' => [
                    'services' => [
                        'a' => $this->createMock(ConfigOptionInterface::class),
                        'b' => $this->createMock(ConfigOptionInterface::class),
                        'c' => $this->createMock(ConfigOptionInterface::class),
                    ],
                ],
            ],
            3,
        ];
    }
}
