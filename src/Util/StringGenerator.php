<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Util;

use function random_int;
use function strlen;

/** @deprecated */
class StringGenerator implements StringGeneratorInterface
{
    private const BASE62 = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    public function generateRandomString(int $length = 10): string
    {
        $characters = self::BASE62;
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
