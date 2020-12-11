<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Container\ContainerInterface;
use ReflectionObject;
use Shlinkio\Shlink\Installer\Config\ConfigOptionsManagerFactory;
use Shlinkio\Shlink\Installer\Config\Option\ConfigOptionInterface;

class ConfigOptionsManagerFactoryTest extends TestCase
{
    use ProphecyTrait;

    private ConfigOptionsManagerFactory $factory;
    private ObjectProphecy $container;

    public function setUp(): void
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->factory = new ConfigOptionsManagerFactory();
    }

    /**
     * @test
     * @dataProvider provideConfigs
     */
    public function createsServiceWithExpectedPlugins(array $config, int $expectedSize): void
    {
        $getConfig = $this->container->get('config')->willReturn($config);

        $service = ($this->factory)($this->container->reveal());
        $ref = new ReflectionObject($service);
        $servicesProp = $ref->getProperty('services');
        $servicesProp->setAccessible(true);

        self::assertCount($expectedSize, $servicesProp->getValue($service));
        $getConfig->shouldHaveBeenCalledOnce();
    }

    public function provideConfigs(): iterable
    {
        yield 'config_options not defined' => [[], 0];
        yield 'config_options empty' => [['config_options' => []], 0];
        yield 'config_options with values' => [
            [
                'config_options' => [
                    'services' => [
                        'a' => $this->prophesize(ConfigOptionInterface::class)->reveal(),
                        'b' => $this->prophesize(ConfigOptionInterface::class)->reveal(),
                        'c' => $this->prophesize(ConfigOptionInterface::class)->reveal(),
                    ],
                ],
            ],
            3,
        ];
    }
}
