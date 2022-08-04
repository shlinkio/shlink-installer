<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Visit;

use Shlinkio\Shlink\Installer\Config\Option\AbstractSwooleDependentConfigOption;
use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidatorsTrait;
use Symfony\Component\Console\Style\StyleInterface;

use function implode;

/** @deprecated */
class VisitsWebhooksConfigOption extends AbstractSwooleDependentConfigOption
{
    use ConfigOptionsValidatorsTrait;

    public const ENV_VAR = 'VISITS_WEBHOOKS';

    public function getEnvVar(): string
    {
        return self::ENV_VAR;
    }

    public function ask(StyleInterface $io, array $currentOptions): string
    {
        return implode(',', $io->ask(
            'Provide a comma-separated list of webhook URLs which will receive POST notifications when short URLs '
            . 'receive visits (Ignore this if you are not serving shlink with swoole or openswoole)',
            null,
            [$this, 'splitAndValidateMultipleUrls'],
        ));
    }
}
