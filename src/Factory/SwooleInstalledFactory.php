<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Factory;

use function extension_loaded;

class SwooleInstalledFactory
{
    public const SWOOLE_INSTALLED = 'Shlinkio\Shlink\Installer\SwooleInstalled';

    public function __invoke(): callable
    {
        return static fn (): bool => extension_loaded('swoole');
    }
}
