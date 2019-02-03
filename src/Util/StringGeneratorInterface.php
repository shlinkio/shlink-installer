<?php
declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Util;

interface StringGeneratorInterface
{
    public function generateRandomString(int $length = 10): string;

    public function generateRandomShortCodeChars(): string;
}
