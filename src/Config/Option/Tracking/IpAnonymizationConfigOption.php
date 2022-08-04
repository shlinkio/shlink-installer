<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Tracking;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\DependentConfigOptionInterface;
use Symfony\Component\Console\Style\StyleInterface;

class IpAnonymizationConfigOption extends BaseConfigOption implements DependentConfigOptionInterface
{
    public function getEnvVar(): string
    {
        return 'ANONYMIZE_REMOTE_ADDR';
    }

    public function shouldBeAsked(array $currentOptions): bool
    {
        $parentShouldBeAsked = parent::shouldBeAsked($currentOptions);
        $disableTracking = $currentOptions[DisableTrackingConfigOption::ENV_VAR] ?? false;
        $disableIpTracking = $currentOptions[DisableIpTrackingConfigOption::ENV_VAR] ?? false;

        return $parentShouldBeAsked && ! $disableTracking && ! $disableIpTracking;
    }

    public function ask(StyleInterface $io, array $currentOptions): bool
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
