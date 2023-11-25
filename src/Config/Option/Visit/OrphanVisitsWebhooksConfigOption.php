<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Visit;

use Shlinkio\Shlink\Installer\Config\Option\DependentConfigOptionInterface;
use Shlinkio\Shlink\Installer\Config\Option\Server\AbstractAsyncRuntimeDependentConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

/** @deprecated */
class OrphanVisitsWebhooksConfigOption extends AbstractAsyncRuntimeDependentConfigOption implements
    DependentConfigOptionInterface
{
    public function getEnvVar(): string
    {
        return 'NOTIFY_ORPHAN_VISITS_TO_WEBHOOKS';
    }

    public function shouldBeAsked(array $currentOptions): bool
    {
        $visitWebhooks = $currentOptions[VisitsWebhooksConfigOption::ENV_VAR] ?? [];
        return ! empty($visitWebhooks) && parent::shouldBeAsked($currentOptions);
    }

    public function ask(StyleInterface $io, array $currentOptions): bool
    {
        return $io->confirm('Do you want to also notify the webhooks when an orphan visit occurs?', false);
    }

    public function getDependentOption(): string
    {
        return VisitsWebhooksConfigOption::class;
    }
}
