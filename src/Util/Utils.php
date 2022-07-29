<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Util;

use Shlinkio\Shlink\Installer\Config\Option\Database\DatabaseDriverConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\UrlShortener\ShortDomainSchemaConfigOption;
use Shlinkio\Shlink\Installer\Config\Util\DatabaseDriver;

use function array_filter;
use function ctype_upper;
use function explode;
use function Functional\every;
use function Functional\map;
use function implode;
use function is_array;
use function is_bool;
use function is_numeric;
use function trim;

use const ARRAY_FILTER_USE_KEY;

class Utils
{
    public static function commaSeparatedToList(string $list): array
    {
        return map(explode(',', $list), static fn (string $value) => trim($value));
    }

    public static function normalizeAndKeepEnvVarKeys(array $array): array
    {
        $dbEnvVar = DatabaseDriverConfigOption::ENV_VAR;

        return map(
            array_filter(
                $array,
                static fn (string $key) => every(explode('_', $key), static fn (string $part) =>
                    ctype_upper($part) || is_numeric($part)),
                ARRAY_FILTER_USE_KEY,
            ),
            // This maps old values that have been imported, to the new expected values
            static fn (mixed $value, string $envVar) => match (true) {
                is_array($value) => implode(',', $value),
                $envVar === ShortDomainSchemaConfigOption::ENV_VAR && ! is_bool($value) => $value === 'https',
                $envVar === $dbEnvVar && $value === 'pdo_pgsql' => DatabaseDriver::POSTGRES->value,
                $envVar === $dbEnvVar && $value === 'pdo_sqlite' => DatabaseDriver::SQLITE->value,
                $envVar === $dbEnvVar && $value === 'pdo_sqlsrv' => DatabaseDriver::MSSQL->value,
                $envVar === $dbEnvVar && $value === 'pdo_mysql' => DatabaseDriver::MYSQL->value,
                default => $value,
            },
        );
    }
}
