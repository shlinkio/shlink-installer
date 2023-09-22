<?php

namespace Shlinkio\Shlink\Installer\Command\Model;

use Shlinkio\Shlink\Installer\Model\CLIOption;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;

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

    public function description(): string
    {
        return match ($this) {
            self::SKIP_INITIALIZE_DB =>
                'Skip the initial empty database creation. It will make this command fail on a later stage if the '
                . 'database was not created manually.',
            self::CLEAR_DB_CACHE => 'Clear the database metadata cache.',
            self::INITIAL_API_KEY =>
                'Create an initial admin API key. A random one will be generated and printed if no value is provided.',
            self::DOWNLOAD_RR_BINARY =>
            'Download a RoadRunner binary. Useful only if you plan to serve Shlink with Roadrunner.',
            self::SKIP_DOWNLOAD_GEOLITE =>
                'Skip downloading the initial GeoLite DB file. Shlink will try to download it the first time it needs '
                . 'to geolocate visits.',
        };
    }

    public function valueType(): int
    {
        return match ($this) {
            self::INITIAL_API_KEY => InputOption::VALUE_OPTIONAL,
            default => InputOption::VALUE_NONE,
        };
    }

    public function defaultValue(): bool|null
    {
        return match ($this) {
            self::INITIAL_API_KEY => false,
            default => null,
        };
    }

    public function toCLIOption(Command $command): CLIOption
    {
        return new CLIOption($command, $this);
    }
}
