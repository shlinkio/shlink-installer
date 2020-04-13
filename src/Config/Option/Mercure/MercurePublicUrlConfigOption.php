<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Mercure;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Util\AskUtilsTrait;
use Symfony\Component\Console\Style\StyleInterface;

class MercurePublicUrlConfigOption extends AbstractMercureEnabledConfigOption
{
    use AskUtilsTrait;

    public function getConfigPath(): array
    {
        return ['mercure', 'public_hub_url'];
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): string
    {
        return $this->askRequired($io, 'public hub URL', 'Public URL of the mercure hub server');
    }
}
