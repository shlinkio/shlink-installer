<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Util;

use Shlinkio\Shlink\Installer\Config\Option\Database\DatabaseDriverConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\UrlShortener\ShortDomainSchemaConfigOption;
use Shlinkio\Shlink\Installer\Config\Util\DatabaseDriver;

use function array_filter;
use function array_reduce;
use function ctype_upper;
use function explode;
use function implode;
use function is_array;
use function is_bool;
use function is_numeric;

use const ARRAY_FILTER_USE_KEY;

class Utils
{
    public static function normalizeAndKeepEnvVarKeys(array $array): array
    {
        $dbEnvVar = DatabaseDriverConfigOption::ENV_VAR;
        $filteredEnvVars = array_filter(
            $array,
            static fn (string $key) =>
                // Filter out env vars which are not fully in uppercase.
                // Numbers are also valid, as some env vars (like `DEFAULT_REGULAR_404_REDIRECT`) contain them.
                array_reduce(
                    explode('_', $key),
                    static fn (bool $carry, string $part) => $carry && (ctype_upper($part) || is_numeric($part)),
                    initial: true,
                ),
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
