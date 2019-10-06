<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Util;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Shlinkio\Shlink\Installer\Config\Util\ExpectedConfigResolverInterface;

trait TestUtilsTrait
{
    private function createExpectedConfigResolverMock(): ExpectedConfigResolverInterface
    {
        /** @var TestCase $this */
        $resolver = $this->prophesize(ExpectedConfigResolverInterface::class);
        $resolver->resolveExpectedKeys(Argument::cetera())->willReturnArgument(1);

        return $resolver->reveal();
    }
}
