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
    case API_KEY_CREATE = 'api_key_create';
    case ROAD_RUNNER_BINARY_DOWNLOAD = 'road_runner_update';
}
