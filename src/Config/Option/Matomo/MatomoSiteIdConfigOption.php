<?php

namespace Shlinkio\Shlink\Installer\Config\Option\Matomo;

use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidator;
use Symfony\Component\Console\Style\StyleInterface;

class MatomoSiteIdConfigOption extends AbstractMatomoEnabledConfigOption
{
    public function getEnvVar(): string
    {
        return 'MATOMO_SITE_ID';
    }

    public function ask(StyleInterface $io, array $currentOptions): string
    {
        $option = 'Matomo site ID';
        return $io->ask(
            $option,
            validator: static fn ($value) => ConfigOptionsValidator::validateRequired($value, $option),
        );
    }
}
