<?php

namespace Shlinkio\Shlink\Installer\Command\Model;

use Shlinkio\Shlink\Installer\Model\FlagOption;
use Symfony\Component\Console\Command\Command;

enum InitOption: string
{
    case SKIP_INITIALIZE_DB = 'skip-initialize-db';
    case CLEAR_DB_CACHE = 'clear-db-cache';
    case INITIAL_API_KEY = 'initial-api-key';
    case DOWNLOAD_RR_BINARY = 'download-rr-binary';
    case SKIP_DOWNLOAD_GEOLITE = 'skip-download-geolite';

    public function asCliFlag(): string
    {
        return '--' . $this->value;
    }

    public function toFlagOption(Command $command): FlagOption
    {
        $description = match ($this) {
            self:: SKIP_INITIALIZE_DB =>
                'Skip the initial empty database creation. It will make this command fail on a later stage if the '
                . 'database was not created manually.',
            self:: CLEAR_DB_CACHE => 'Clear the database metadata cache.',
            self:: INITIAL_API_KEY => 'Create and print initial admin API key.',
            self:: DOWNLOAD_RR_BINARY =>
                'Download a RoadRunner binary. Useful only if you plan to serve Shlink with Roadrunner.',
            self:: SKIP_DOWNLOAD_GEOLITE =>
                'Skip downloading the initial GeoLite DB file. Shlink will try to download it the first time it needs '
                . 'to geolocate visits.',
        };
        return new FlagOption($command, $this->value, $description);
    }
}
