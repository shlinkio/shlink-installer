<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Visit;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidator;
use Symfony\Component\Console\Style\StyleInterface;

class VisitsThresholdConfigOption extends BaseConfigOption
{
    public function getEnvVar(): string
    {
        return 'DELETE_SHORT_URL_THRESHOLD';
    }

    public function ask(StyleInterface $io, array $currentOptions): int|null
    {
        $result = $io->ask(
            'What is the amount of visits from which the system will not allow short URLs to be deleted? Leave empty '
            . 'to always allow deleting short URLs, no matter what',
            null,
            ConfigOptionsValidator::validateOptionalPositiveNumber(...),
        );

        return $result === null ? null : (int) $result;
    }
}
