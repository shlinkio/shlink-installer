<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Util;

use Shlinkio\Shlink\Installer\Config\Option\Database\DatabaseDriverConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\UrlShortener\ShortDomainSchemaConfigOption;
use Shlinkio\Shlink\Installer\Config\Util\DatabaseDriver;

use function array_filter;
use function array_map;
use function ctype_upper;
use function explode;
use function Functional\every;
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
        return array_map(static fn (string $value) => trim($value), explode(',', $list));
    }

    public static function normalizeAndKeepEnvVarKeys(array $array): array
    {
        $dbEnvVar = DatabaseDriverConfigOption::ENV_VAR;
        $filteredEnvVars = array_filter(
            $array,
            static fn (string $key) =>
                // Filter out env vars which are not fully in uppercase.
                // Numbers are also valid, as some env vars (like `DEFAULT_REGULAR_404_REDIRECT`) contain them.
                every(explode('_', $key), static fn (string $part) => ctype_upper($part) || is_numeric($part)),
            ARRAY_FILTER_USE_KEY,
        );

        foreach ($filteredEnvVars as $envVar => $value) {
            $filteredEnvVars[$envVar] = match (true) {
                is_array($value) => implode(',', $value),
                $envVar === ShortDomainSchemaConfigOption::ENV_VAR && ! is_bool($value) => $value === 'https',
                $envVar === $dbEnvVar && $value === 'pdo_pgsql' => DatabaseDriver::POSTGRES->value,
                $envVar === $dbEnvVar && $value === 'pdo_sqlite' => DatabaseDriver::SQLITE->value,
                $envVar === $dbEnvVar && $value === 'pdo_sqlsrv' => DatabaseDriver::MSSQL->value,
                $envVar === $dbEnvVar && $value === 'pdo_mysql' => DatabaseDriver::MYSQL->value,
                default => $value,
            };
        }

        return $filteredEnvVars;
    }
}
