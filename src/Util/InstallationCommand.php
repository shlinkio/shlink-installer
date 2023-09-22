<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Util;

use Shlinkio\Shlink\Installer\Model\ShlinkInitConfig;

use function is_string;

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

    /**
     * @return iterable<array{self, string | null}>
     */
    public static function resolveCommandsForConfig(ShlinkInitConfig $config): iterable
    {
        if ($config->initializeDb) {
            yield [self::DB_CREATE_SCHEMA, null];
        }

        yield [self::DB_MIGRATE, null];
        yield [self::ORM_PROXIES, null];

        if ($config->clearDbCache) {
            yield [self::ORM_CLEAR_CACHE, null];
        }

        if ($config->downloadGeoLiteDb) {
            yield [self::GEOLITE_DOWNLOAD_DB, null];
        }

        if ($config->generateApiKey === null) {
            yield [self::API_KEY_GENERATE, null];
        } elseif (is_string($config->generateApiKey)) {
            yield [self::API_KEY_CREATE, $config->generateApiKey];
        }

        if ($config->downloadRoadrunnerBinary) {
            yield [self::ROAD_RUNNER_BINARY_DOWNLOAD, null];
        }
    }
}
