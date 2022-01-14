<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Util;

use function array_filter;
use function ctype_upper;
use function explode;
use function Functional\map;
use function str_replace;
use function trim;

use const ARRAY_FILTER_USE_KEY;

class Utils
{
    public static function commaSeparatedToList(string $list): array
    {
        return map(explode(',', $list), static fn (string $value) => trim($value));
    }

    public static function keepEnvVarKeys(array $array): array
    {
        return array_filter(
            $array,
            static fn (string $key) => ctype_upper(str_replace('_', '', $key)),
            ARRAY_FILTER_USE_KEY,
        );
    }
}
