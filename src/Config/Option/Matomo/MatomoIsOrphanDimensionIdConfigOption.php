<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Matomo;

use Override;
use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidator;
use Symfony\Component\Console\Style\StyleInterface;

class MatomoIsOrphanDimensionIdConfigOption extends AbstractMatomoEnabledConfigOption
{
    #[Override]
    public function getEnvVar(): string
    {
        return 'MATOMO_IS_ORPHAN_DIMENSION_ID';
    }

    #[Override]
    public function ask(StyleInterface $io, array $currentOptions): int|null
    {
        return $io->ask(
            'ID for the custom dimension where is_orphan flag will be set',
            validator: ConfigOptionsValidator::validateOptionalPositiveNumber(...),
        );
    }
}
