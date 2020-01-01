<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Shlinkio\Shlink\Installer\Util\AskUtilsTrait;
use Symfony\Component\Console\Style\SymfonyStyle;

class ShortDomainHostConfigOption extends BaseConfigOption
{
    use AskUtilsTrait;

    public function getConfigPath(): array
    {
        return ['url_shortener', 'domain', 'hostname'];
    }

    public function ask(SymfonyStyle $io, array $currentOptions)
    {
        return $this->askRequired($io, 'domain', 'Default domain for generated short URLs');
    }
}
