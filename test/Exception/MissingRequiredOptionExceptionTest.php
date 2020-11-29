<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Exception;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Exception\MissingRequiredOptionException;

class MissingRequiredOptionExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function fromOptionsGeneratesExpectedMessage(): void
    {
        $e = MissingRequiredOptionException::fromOption('foo');
        self::assertEquals('The "foo" is required and can\'t be empty', $e->getMessage());
    }
}
