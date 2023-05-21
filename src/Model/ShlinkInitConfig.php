<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Model;

final class ShlinkInitConfig
{
    public function __construct(
        public readonly bool $initializeDb,
        public readonly bool $clearDbCache,
        public readonly bool $updateRoadrunnerBinary,
        public readonly bool $generateApiKey,
        public readonly bool $downloadGeoLiteDb,
    ) {
    }
}
