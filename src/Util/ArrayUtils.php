<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Util;

use function array_reduce;
use function in_array;

final class ArrayUtils
{
    public static function contains(mixed $value, array $array): bool
    {
        return in_array($value, $array, strict: true);
    }

    /**
     * @param array[] $multiArray
     * @return array
     */
    public static function flatten(array $multiArray): array
    {
        return array_reduce(
            $multiArray,
            static fn (array $carry, array $value) => [...$carry, ...$value],
            initial: [],
        );
    }
}
