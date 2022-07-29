<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Mercure;

use Shlinkio\Shlink\Installer\Util\AskUtilsTrait;
use Symfony\Component\Console\Style\StyleInterface;

class MercurePublicUrlConfigOption extends AbstractMercureEnabledConfigOption
{
    use AskUtilsTrait;

    public function getEnvVar(): string
    {
        return 'MERCURE_PUBLIC_HUB_URL';
    }

    public function ask(StyleInterface $io, array $currentOptions): string
    {
        return $this->askRequired($io, 'public hub URL', 'Public URL of the mercure hub server');
    }
}
