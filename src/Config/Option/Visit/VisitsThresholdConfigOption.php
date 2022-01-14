<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Visit;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidatorsTrait;
use Symfony\Component\Console\Style\StyleInterface;

class VisitsThresholdConfigOption extends BaseConfigOption
{
    use ConfigOptionsValidatorsTrait;

    public function getDeprecatedPath(): array
    {
        return ['delete_short_urls', 'visits_threshold'];
    }

    public function getEnvVar(): string
    {
        return 'DELETE_SHORT_URL_THRESHOLD';
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): ?int
    {
        return $io->ask(
            'What is the amount of visits from which the system will not allow short URLs to be deleted? Leave empty '
            . 'to always allow deleting short URLs, no matter what.',
            null,
            fn (mixed $value) => $value === null || $this->validatePositiveNumber($value),
        );
    }
}
