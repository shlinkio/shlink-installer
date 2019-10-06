<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Util;

use PHPUnit\Framework\TestCase;
use ReflectionObject;
use Shlinkio\Shlink\Installer\Config\Util\ExpectedConfigResolver;
use Shlinkio\Shlink\Installer\Config\Util\ExpectedConfigResolverFactory;
use Zend\ServiceManager\ServiceManager;

class ExpectedConfigResolverFactoryTest extends TestCase
{
    /** @var ExpectedConfigResolverFactory */
    private $factory;

    public function setUp(): void
    {
        $this->factory = new ExpectedConfigResolverFactory();
    }

    /** @test */
    public function createsInstance(): void
    {
        $instance = $this->create();
        $this->assertInstanceOf(ExpectedConfigResolver::class, $instance);
    }

    /**
     * @test
     * @dataProvider provideConfigMaps
     */
    public function injectsProperConfigMap(?array $configMap, array $expected): void
    {
        $instance = $this->create(['installer_plugins_expected_config' => $configMap]);
        $ref = new ReflectionObject($instance);
        $prop = $ref->getProperty('expectedKeysMap');
        $prop->setAccessible(true);

        $this->assertEquals($expected, $prop->getValue($instance));
    }

    public function provideConfigMaps(): iterable
    {
        yield [null, []];
        yield [['foo' => ['bar']], ['foo' => ['bar']]];
    }

    private function create(array $config = [])
    {
        return ($this->factory)(new ServiceManager(['services' => [
            'config' => $config,
        ]]), '');
    }
}
