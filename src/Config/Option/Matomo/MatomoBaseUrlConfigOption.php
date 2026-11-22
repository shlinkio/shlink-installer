<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Matomo;

use Override;
use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidator;
use Symfony\Component\Console\Style\StyleInterface;

class MatomoBaseUrlConfigOption extends AbstractMatomoEnabledConfigOption
{
    #[Override]
    public function getEnvVar(): string
    {
        return 'MATOMO_BASE_URL';
    }

    #[Override]
    public function ask(StyleInterface $io, array $currentOptions): string
    {
        $option = 'Matomo server URL';
        return $io->ask(
            $option,
            validator: static fn ($value) => ConfigOptionsValidator::validateRequired($value, $option),
        );
    }
}
