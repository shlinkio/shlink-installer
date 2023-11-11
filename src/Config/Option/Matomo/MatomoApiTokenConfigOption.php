<?php

namespace Shlinkio\Shlink\Installer\Config\Option\Matomo;

use Shlinkio\Shlink\Installer\Util\AskUtilsTrait;
use Symfony\Component\Console\Style\StyleInterface;

class MatomoApiTokenConfigOption extends AbstractMatomoEnabledConfigOption
{
    use AskUtilsTrait;

    public function getEnvVar(): string
    {
        return 'MATOMO_API_TOKEN';
    }

    public function ask(StyleInterface $io, array $currentOptions): string
    {
        return $this->askRequired($io, 'Matomo API token');
    }
}
