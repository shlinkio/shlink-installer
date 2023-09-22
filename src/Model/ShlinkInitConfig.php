<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Model;

final class ShlinkInitConfig
{
    public function __construct(
        public readonly bool $initializeDb,
        public readonly bool $clearDbCache,
        public readonly bool $downloadRoadrunnerBinary,
        /**
         * False: Do not generate an initial API key.
         * String: Use provided value as the initial API key.
         * Null: Auto-generate a random initial API key.
         * @todo Change to string|false|null once PHP 8.1 is no longer supported
         */
        public readonly string|bool|null $generateApiKey,
        public readonly bool $downloadGeoLiteDb,
    ) {
    }
}
