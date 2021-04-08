<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Util;

final class InstallationCommand
{
    public const DB_CREATE_SCHEMA = 'db_create_schema';
    public const DB_MIGRATE = 'db_migrate';
    public const ORM_PROXIES = 'orm_proxies';
    public const ORM_CLEAR_CACHE = 'orm_clear_cache';
    public const GEOLITE_DOWNLOAD_DB = 'geolite_download_db';

    public const POST_INSTALL_COMMANDS = [
        self::DB_CREATE_SCHEMA,
        self::DB_MIGRATE,
        self::ORM_PROXIES,
        self::GEOLITE_DOWNLOAD_DB,
    ];
    public const POST_UPDATE_COMMANDS = [
        self::DB_MIGRATE,
        self::ORM_PROXIES,
        self::ORM_CLEAR_CACHE,
        self::GEOLITE_DOWNLOAD_DB,
    ];
}
