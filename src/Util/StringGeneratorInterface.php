<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Util;

/** @deprecated */
interface StringGeneratorInterface
{
    public function generateRandomString(int $length = 10): string;
}
