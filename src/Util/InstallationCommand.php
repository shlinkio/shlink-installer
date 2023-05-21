<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Util;

use Shlinkio\Shlink\Installer\Model\ShlinkInitConfig;

enum InstallationCommand: string
{
    case DB_CREATE_SCHEMA = 'db_create_schema';
    case DB_MIGRATE = 'db_migrate';
    case ORM_PROXIES = 'orm_proxies';
    case ORM_CLEAR_CACHE = 'orm_clear_cache';
    case GEOLITE_DOWNLOAD_DB = 'geolite_download_db';
    case API_KEY_GENERATE = 'api_key_generate';
    case ROAD_RUNNER_BINARY_DOWNLOAD = 'road_runner_update';

    /**
     * @return iterable<self>
     */
    public static function resolveCommandsForConfig(ShlinkInitConfig $config): iterable
    {
        if ($config->initializeDb) {
            yield self::DB_CREATE_SCHEMA;
        }

        yield self::DB_MIGRATE;
        yield self::ORM_PROXIES;

        if ($config->clearDbCache) {
            yield self::ORM_CLEAR_CACHE;
        }

        if ($config->downloadGeoLiteDb) {
            yield self::GEOLITE_DOWNLOAD_DB;
        }

        if ($config->generateApiKey) {
            yield self::API_KEY_GENERATE;
        }

        if ($config->updateRoadrunnerBinary) {
            yield self::ROAD_RUNNER_BINARY_DOWNLOAD;
        }
    }
}
