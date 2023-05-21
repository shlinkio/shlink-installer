<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Model;

final class ShlinkInitConfig
{
    public function __construct(
        public readonly bool $initializeDb,
        public readonly bool $clearDbCache,
        public readonly bool $isRoadRunnerInstance,
        public readonly bool $generateApiKey,
    ) {
    }
}
