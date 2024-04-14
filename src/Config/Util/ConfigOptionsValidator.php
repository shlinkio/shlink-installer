<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Util;

use Shlinkio\Shlink\Installer\Exception\InvalidConfigOptionException;

use function ctype_xdigit;
use function is_numeric;
use function ltrim;
use function preg_match;
use function sprintf;
use function strlen;

class ConfigOptionsValidator
{
    public static function validateUrl(?string $value): ?string
    {
        $httpUrlRegexp =
            '/https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}'
            . '\b([-a-zA-Z0-9()@:%_\+.~#?&\/\/=]*)/i';
        $valueIsValid = $value === null || (bool) preg_match($httpUrlRegexp, $value);

        if (! $valueIsValid) {
            throw new InvalidConfigOptionException(
                sprintf('Provided value "%s" is not a valid URL', $value),
            );
        }

        return $value;
    }

    public static function validateOptionalPositiveNumber(mixed $value): ?int
    {
        return $value === null ? $value : self::validatePositiveNumber($value);
    }

    public static function validatePositiveNumber(mixed $value): int
    {
        return self::validateNumberGreaterThan($value, 1);
    }

    public static function validateNumberGreaterThan(mixed $value, int $min): int
    {
        $intValue = (int) $value;
        if (! is_numeric($value) || $intValue < $min) {
            throw new InvalidConfigOptionException(
                sprintf('Provided value "%s" is invalid. Expected a number greater or equal than %s', $value, $min),
            );
        }

        return $intValue;
    }

    public static function validateNumberBetween(mixed $value, int $min, int $max): int
    {
        $intValue = (int) $value;
        if (! is_numeric($value) || $intValue < $min || $intValue > $max) {
            throw new InvalidConfigOptionException(
                sprintf('Provided value "%s" is invalid. Expected a number between %s and %s', $value, $min, $max),
            );
        }

        return $intValue;
    }

    public static function validateHexColor(string $color): string
    {
        $onlyDigitsColor = ltrim($color, '#');
        $digitsLength = strlen($onlyDigitsColor);

        if ($digitsLength !== 3 && $digitsLength !== 6) {
            throw new InvalidConfigOptionException(
                'Provided value must have 3 or 6 characters, and be optionally preceded by the # character',
            );
        }

        if (! ctype_xdigit($onlyDigitsColor)) {
            throw new InvalidConfigOptionException(
                'Provided value must be the hexadecimal number representation of a color',
            );
        }

        return $color;
    }

    public static function validateMemoryValue(string $value): string
    {
        if (preg_match('/^\d+([KMG])?$/i', $value) === 1) {
            return $value;
        }

        throw new InvalidConfigOptionException(
            'Provided value is invalid. It should be an amount in bytes (1024), or a number followed by K, M, or G '
            . '(512M, 1G)',
        );
    }
}
