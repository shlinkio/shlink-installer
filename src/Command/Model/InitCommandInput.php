<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Command\Model;

use Shlinkio\Shlink\Installer\Util\InstallationCommand;
use Symfony\Component\Console\Attribute\Option;

use function is_string;

final class InitCommandInput
{
    #[Option(
        'Skip the initial empty database creation. It will make this command fail on a later stage if the '
        . 'database was not created manually',
    )]
    public bool $skipInitializeDb = false;

    #[Option('Clear the database metadata cache')]
    public bool $clearDbCache = false;

    /**
     * False: Do not generate an initial API key.
     * True: Auto-generate a random initial API key.
     * String: Use provided value as the initial API key.
     */
    #[Option('Create an initial API key. A random one will be generated and printed if no value is provided')]
    public string|bool $initialApiKey = false;

    #[Option('Download a RoadRunner binary. Useful only if you plan to serve Shlink with Roadrunner')]
    public bool $downloadRrBinary = false;

    #[Option(
        'Skip downloading the initial GeoLite DB file. Shlink will try to download it the first time it needs '
        . 'to geolocate visits',
    )]
    public bool $skipDownloadGeolite = false;

    /**
     * @return iterable<array{InstallationCommand, string|null}>
     */
    public function resolveCommands(): iterable
    {
        if (! $this->skipInitializeDb) {
            yield [InstallationCommand::DB_CREATE_SCHEMA, null];
        }

        yield [InstallationCommand::DB_MIGRATE, null];
        yield [InstallationCommand::ORM_PROXIES, null];

        if ($this->clearDbCache) {
            yield [InstallationCommand::ORM_CLEAR_CACHE, null];
        }

        if (! $this->skipDownloadGeolite) {
            yield [InstallationCommand::GEOLITE_DOWNLOAD_DB, null];
        }

        if ($this->initialApiKey === true) {
            yield [InstallationCommand::API_KEY_GENERATE, null];
        } elseif (is_string($this->initialApiKey)) {
            yield [InstallationCommand::API_KEY_CREATE, $this->initialApiKey];
        }

        if ($this->downloadRrBinary) {
            yield [InstallationCommand::ROAD_RUNNER_BINARY_DOWNLOAD, null];
        }
    }
}
