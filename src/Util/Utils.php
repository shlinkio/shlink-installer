<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Util;

use function explode;
use function Functional\map;
use function trim;

class Utils
{
    public static function commaSeparatedToList(string $list): array
    {
        return map(explode(',', $list), static fn (string $value) => trim($value));
    }
}
