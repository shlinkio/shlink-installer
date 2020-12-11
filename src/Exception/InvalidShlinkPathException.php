<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Exception;

use RuntimeException;

class InvalidShlinkPathException extends RuntimeException implements ExceptionInterface
{
    public static function forCurrentPath(): self
    {
        return new self(
            'This command needs to be run inside a Shlink installation directory where the "install" command has been '
            . 'run first',
        );
    }
}
