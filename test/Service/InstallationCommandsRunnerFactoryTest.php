<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Service;

use PHPUnit\Framework\TestCase;
use ReflectionObject;
use Shlinkio\Shlink\Installer\Service;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Process\PhpExecutableFinder;
use Zend\ServiceManager\ServiceManager;

class InstallationCommandsRunnerFactoryTest extends TestCase
{
    private Service\InstallationCommandsRunnerFactory $factory;

    public function setUp(): void
    {
        $this->factory = new Service\InstallationCommandsRunnerFactory();
    }

    /** @test */
    public function createsServiceWhenInvoked(): void
    {
        $expectedPhpExecutable = '/foo/bar/php';
        $phpFinder = $this->prophesize(PhpExecutableFinder::class);
        $findPhp = $phpFinder->find(false)->willReturn($expectedPhpExecutable);
        $processHelper = $this->prophesize(ProcessHelper::class)->reveal();

        $instance = ($this->factory)(new ServiceManager(['services' => [
            'config' => [],
            ProcessHelper::class => $processHelper,
            PhpExecutableFinder::class => $phpFinder->reveal(),
        ]]));

        $findPhp->shouldHaveBeenCalledOnce();
        $this->assertSame($processHelper, $this->getPropFromInstance($instance, 'processHelper'));
        $this->assertSame([], $this->getPropFromInstance($instance, 'commandsMapping'));
        $this->assertSame($expectedPhpExecutable, $this->getPropFromInstance($instance, 'phpBinary'));
    }

    private function getPropFromInstance(Service\InstallationCommandsRunner $instance, string $propName)
    {
        $ref = new ReflectionObject($instance);
        $prop = $ref->getProperty($propName);
        $prop->setAccessible(true);

        return $prop->getValue($instance);
    }
}
