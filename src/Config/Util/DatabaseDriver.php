<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Util;

enum DatabaseDriver: string
{
    case MYSQL = 'mysql';
    case POSTGRES = 'postgres';
    case MSSQL = 'mssql';
    case SQLITE = 'sqlite';

    public function defaultPort(): ?string
    {
        return match ($this) {
            self::MYSQL => '3306',
            self::POSTGRES => '5432',
            self::MSSQL => '1433',
            self::SQLITE => null,
        };
    }
}
