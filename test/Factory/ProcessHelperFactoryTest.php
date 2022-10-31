<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Factory;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Factory\ProcessHelperFactory;

class ProcessHelperFactoryTest extends TestCase
{
    private ProcessHelperFactory $factory;

    public function setUp(): void
    {
        $this->factory = new ProcessHelperFactory();
    }

    /** @test */
    public function createsTheServiceWithTheProperSetOfHelpers(): void
    {
        $processHelper = ($this->factory)();
        $helperSet = $processHelper->getHelperSet();

        self::assertNotNull($helperSet);
        self::assertCount(2, $helperSet);
        self::assertTrue($helperSet->has('formatter'));
        self::assertTrue($helperSet->has('debug_formatter'));
    }
}
