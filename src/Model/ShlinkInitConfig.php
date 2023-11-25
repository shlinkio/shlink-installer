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
         */
        public readonly false|string|null $generateApiKey,
        public readonly bool $downloadGeoLiteDb,
    ) {
    }
}
