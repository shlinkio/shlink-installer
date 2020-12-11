<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Exception;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Exception\InvalidShlinkPathException;

class InvalidShlinkPathExceptionTest extends TestCase
{
    /** @test */
    public function exceptionIsCreatedAsExpected(): void
    {
        $e = InvalidShlinkPathException::forCurrentPath();

        self::assertEquals(
            'This command needs to be run inside a Shlink installation directory where the "install" command has been '
            . 'run first',
            $e->getMessage(),
        );
    }
}
