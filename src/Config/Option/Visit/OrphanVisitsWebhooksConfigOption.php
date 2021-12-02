<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Visit;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\AbstractSwooleDependentConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\DependentConfigOptionInterface;
use Symfony\Component\Console\Style\StyleInterface;

class OrphanVisitsWebhooksConfigOption extends AbstractSwooleDependentConfigOption implements
    DependentConfigOptionInterface
{
    public function getConfigPath(): array
    {
        return ['url_shortener', 'notify_orphan_visits_to_webhooks'];
    }

    public function shouldBeAsked(PathCollection $currentOptions): bool
    {
        $visitWebhooks = $currentOptions->getValueInPath(VisitsWebhooksConfigOption::CONFIG_PATH);
        return ! empty($visitWebhooks) && parent::shouldBeAsked($currentOptions);
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): bool
    {
        return $io->confirm('Do you want to also notify the webhooks when an orphan visit occurs?', false);
    }

    public function getDependentOption(): string
    {
        return VisitsWebhooksConfigOption::class;
    }
}
