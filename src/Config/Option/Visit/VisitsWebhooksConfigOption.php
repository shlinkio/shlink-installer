<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Visit;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\AbstractSwooleDependentConfigOption;
use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidatorsTrait;
use Symfony\Component\Console\Style\StyleInterface;

class VisitsWebhooksConfigOption extends AbstractSwooleDependentConfigOption
{
    use ConfigOptionsValidatorsTrait;

    public const CONFIG_PATH = ['url_shortener', 'visits_webhooks'];

    public function ask(StyleInterface $io, PathCollection $currentOptions): array
    {
        return $io->ask(
            'Provide a comma-separated list of webhook URLs which will receive POST notifications when short URLs '
            . 'receive visits (Ignore this if you are not serving shlink with swoole)',
            null,
            [$this, 'splitAndValidateMultipleUrls'],
        );
    }

    public function getConfigPath(): array
    {
        return self::CONFIG_PATH;
    }
}
