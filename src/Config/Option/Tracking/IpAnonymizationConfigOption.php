<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Tracking;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\AbstractWithDeprecatedPathConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\DependentConfigOptionInterface;
use Symfony\Component\Console\Style\StyleInterface;

class IpAnonymizationConfigOption extends AbstractWithDeprecatedPathConfigOption implements
    DependentConfigOptionInterface
{
    public function getConfigPath(): array
    {
        return ['tracking', 'anonymize_remote_addr'];
    }

    protected function getDeprecatedPath(): array
    {
        return ['url_shortener', 'anonymize_remote_addr'];
    }

    public function shouldBeAsked(PathCollection $currentOptions): bool
    {
        $parentShouldBeAsked = parent::shouldBeAsked($currentOptions);
        $disableTracking = $currentOptions->getValueInPath(DisableTrackingConfigOption::CONFIG_PATH);
        $disableIpTracking = $currentOptions->getValueInPath(DisableIpTrackingConfigOption::CONFIG_PATH);

        return $parentShouldBeAsked && ! $disableTracking && ! $disableIpTracking;
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): bool
    {
        $anonymize = $io->confirm(
            'Do you want visitors\' remote IP addresses to be anonymized before persisting them to the database?',
        );
        if ($anonymize) {
            return true;
        }

        $io->warning(
            'Careful! If you disable IP address anonymization, you will no longer be in compliance with the GDPR and '
            . 'other similar data protection regulations.',
        );
        return ! $io->confirm('Do you still want to disable anonymization?', false);
    }

    public function getDependentOption(): string
    {
        return DisableIpTrackingConfigOption::class;
    }
}
