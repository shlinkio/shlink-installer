<?php
declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Service;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Service;
use Zend\ServiceManager\ServiceManager;

class InstallationCommandsRunnerFactoryTest extends TestCase
{
    /** @var Service\InstallationCommandsRunnerFactory */
    private $factory;

    public function setUp(): void
    {
        $this->factory = new Service\InstallationCommandsRunnerFactory();
    }

    /**
     * @test
     */
    public function createsServiceWhenInvoked(): void
    {
        $instance = ($this->factory)(new ServiceManager(['services' => [
            'config' => [],
        ]]), '');
        $this->assertInstanceOf(Service\InstallationCommandsRunner::class, $instance);
    }
}
