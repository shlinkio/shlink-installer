<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Visit;

use Shlinkio\Shlink\Installer\Config\Option\Server\AbstractAsyncRuntimeDependentConfigOption;
use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidatorsTrait;
use Symfony\Component\Console\Style\StyleInterface;

use function implode;

/** @deprecated */
class VisitsWebhooksConfigOption extends AbstractAsyncRuntimeDependentConfigOption
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
            . 'receive visits.',
            null,
            [$this, 'splitAndValidateMultipleUrls'],
        ));
    }
}
