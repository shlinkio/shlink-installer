<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Util;

use Shlinkio\Shlink\Installer\Exception\InvalidConfigOptionException;

use function explode;
use function Functional\map;
use function is_numeric;
use function preg_match;
use function sprintf;

trait ConfigOptionsValidatorsTrait
{
    public function validateUrl(?string $value): ?string
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

    public function validatePositiveNumber(mixed $value, int $min = 1): int
    {
        if (! is_numeric($value) || $min > (int) $value) {
            throw new InvalidConfigOptionException(
                sprintf('Provided value "%s" is invalid. Expected a number greater than %s', $value, $min),
            );
        }

        return (int) $value;
    }

    public function splitAndValidateMultipleUrls(?string $urls): array
    {
        if ($urls === null) {
            return [];
        }

        $splitUrls = explode(',', $urls);
        return map($splitUrls, [$this, 'validateUrl']);
    }
}
