<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config;

use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Container\ContainerInterface;
use ReflectionObject;
use Shlinkio\Shlink\Installer\Config\ConfigGeneratorFactory;

class ConfigGeneratorFactoryTest extends TestCase
{
    private ConfigGeneratorFactory $factory;
    private ObjectProphecy $container;

    public function setUp(): void
    {
        $this->factory = new ConfigGeneratorFactory();
        $this->container = $this->prophesize(ContainerInterface::class);
    }

    /**
     * @test
     * @dataProvider provideConfig
     */
    public function createsObjectWithConfigFoundInContainer(
        array $config,
        array $expectedGroups,
        ?array $expectedEnabled
    ): void {
        $getConfig = $this->container->get('config')->willReturn($config);

        $generator = ($this->factory)($this->container->reveal());
        $ref = new ReflectionObject($generator);
        $configOptionsGroupsProp = $ref->getProperty('configOptionsGroups');
        $configOptionsGroupsProp->setAccessible(true);
        $enabledOptionsProp = $ref->getProperty('enabledOptions');
        $enabledOptionsProp->setAccessible(true);

        $this->assertEquals($expectedGroups, $configOptionsGroupsProp->getValue($generator));
        $this->assertEquals($expectedEnabled, $enabledOptionsProp->getValue($generator));
        $getConfig->shouldHaveBeenCalledOnce();
    }

    public function provideConfig(): iterable
    {
        yield [[], [], null];
        yield [['config_options' => []], [], null];
        yield [['installer' => []], [], null];
        yield [['config_options' => [], 'installer' => []], [], null];
        yield [
            [
                'config_options' => [],
                'installer' => [
                    'enabled_options' => $enabled = ['foo', 'bar'],
                ],
            ],
            [],
            $enabled,
        ];
        yield [
            [
                'config_options' => [
                    'groups' => $groups = ['foo', 'bar'],
                ],
                'installer' => [
                    'enabled_options' => $enabled = ['foo', 'bar'],
                ],
            ],
            $groups,
            $enabled,
        ];
        yield [
            [
                'config_options' => [
                    'groups' => $groups = ['foo', 'bar'],
                ],
                'installer' => [],
            ],
            $groups,
            null,
        ];
    }
}
