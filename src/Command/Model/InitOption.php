<?php

namespace Shlinkio\Shlink\Installer\Command\Model;

/** @deprecated */
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
}
