<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Util;

enum InstallationCommand: string
{
    case DB_CREATE_SCHEMA = 'db_create_schema';
    case DB_MIGRATE = 'db_migrate';
    case ORM_PROXIES = 'orm_proxies';
    case ORM_CLEAR_CACHE = 'orm_clear_cache';
    case GEOLITE_DOWNLOAD_DB = 'geolite_download_db';
    case API_KEY_GENERATE = 'api_key_generate';

    public const POST_INSTALL_COMMANDS = [
        self::DB_CREATE_SCHEMA,
        self::DB_MIGRATE,
        self::ORM_PROXIES,
        self::GEOLITE_DOWNLOAD_DB,
        self::API_KEY_GENERATE,
    ];
    public const POST_UPDATE_COMMANDS = [
        self::DB_MIGRATE,
        self::ORM_PROXIES,
        self::ORM_CLEAR_CACHE,
        self::GEOLITE_DOWNLOAD_DB,
    ];
}
