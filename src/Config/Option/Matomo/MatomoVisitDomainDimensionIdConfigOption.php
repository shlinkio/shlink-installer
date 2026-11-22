<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Matomo;

use Override;
use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidator;
use Symfony\Component\Console\Style\StyleInterface;

class MatomoVisitDomainDimensionIdConfigOption extends AbstractMatomoEnabledConfigOption
{
    #[Override]
    public function getEnvVar(): string
    {
        return 'MATOMO_VISIT_DOMAIN_DIMENSION_ID';
    }

    #[Override]
    public function ask(StyleInterface $io, array $currentOptions): int|null
    {
        return $io->ask(
            'ID for the custom dimension where visit domain will be set',
            validator: ConfigOptionsValidator::validateOptionalPositiveNumber(...),
        );
    }
}
