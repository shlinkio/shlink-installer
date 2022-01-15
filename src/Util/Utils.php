<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Util;

use Shlinkio\Shlink\Installer\Config\Option\Database\DatabaseDriverConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\UrlShortener\ShortDomainSchemaConfigOption;

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
        return map(
            array_filter(
                $array,
                static fn (string $key) => every(explode('_', $key), static fn (string $part) =>
                    ctype_upper($part) || is_numeric($part)),
                ARRAY_FILTER_USE_KEY,
            ),
            // [Deprecated] This maps old values that have been imported, to the new expected values
            static fn (mixed $value, string $key) => match (true) {
                is_array($value) => implode(',', $value),
                $key === ShortDomainSchemaConfigOption::ENV_VAR && ! is_bool($value) => $value === 'https',
                $key === DatabaseDriverConfigOption::ENV_VAR && $value === 'pdo_pgsql' => 'postgres',
                $key === DatabaseDriverConfigOption::ENV_VAR && $value === 'pdo_sqlite' => 'sqlite',
                $key === DatabaseDriverConfigOption::ENV_VAR && $value === 'pdo_sqlsrv' => 'mssql',
                $key === DatabaseDriverConfigOption::ENV_VAR && $value === 'pdo_mysql' => 'mysql',
                default => $value,
            },
        );
    }
}
