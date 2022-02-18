<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Util;

use Shlinkio\Shlink\Installer\Exception\InvalidConfigOptionException;
use Shlinkio\Shlink\Installer\Util\Utils;

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

    public function splitAndValidateMultipleUrls(?string $urls): array
    {
        if ($urls === null) {
            return [];
        }

        $splitUrls = Utils::commaSeparatedToList($urls);
        return map($splitUrls, [$this, 'validateUrl']);
    }

    public function validateOptionalPositiveNumber(mixed $value): ?int
    {
        return $value === null ? $value : $this->validatePositiveNumber($value);
    }

    public function validatePositiveNumber(mixed $value): int
    {
        return $this->validateNumberGreaterThan($value, 1);
    }

    public function validateNumberGreaterThan(mixed $value, int $min): int
    {
        $intValue = (int) $value;
        if (! is_numeric($value) || $intValue < $min) {
            throw new InvalidConfigOptionException(
                sprintf('Provided value "%s" is invalid. Expected a number greater or equal than %s', $value, $min),
            );
        }

        return $intValue;
    }

    public function validateNumberBetween(mixed $value, int $min, int $max): int
    {
        $intValue = (int) $value;
        if (! is_numeric($value) || $intValue < $min || $intValue > $max) {
            throw new InvalidConfigOptionException(
                sprintf('Provided value "%s" is invalid. Expected a number between %s and %s', $value, $min, $max),
            );
        }

        return $intValue;
    }
}
