<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Factory;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Factory\SwooleInstalledFactory;

class SwooleInstalledFactoryTest extends TestCase
{
    private SwooleInstalledFactory $factory;

    public function setUp(): void
    {
        $this->factory = new SwooleInstalledFactory();
    }

    /** @test */
    public function properlyCreatesHelperFunction(): void
    {
        $func = ($this->factory)();

        self::assertFalse($func());
    }
}
