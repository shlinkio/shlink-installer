<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\UrlShortener;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\DependentConfigOptionInterface;
use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidator;
use Shlinkio\Shlink\Installer\Util\ArrayUtils;
use Symfony\Component\Console\Style\StyleInterface;

class RedirectCacheLifeTimeConfigOption extends BaseConfigOption implements DependentConfigOptionInterface
{
    public function getEnvVar(): string
    {
        return 'REDIRECT_CACHE_LIFETIME';
    }

    public function shouldBeAsked(array $currentOptions): bool
    {
        $redirectStatus = $currentOptions[RedirectStatusCodeConfigOption::ENV_VAR] ?? null;
        return $this->isPermanentRedirectStatus($redirectStatus) && parent::shouldBeAsked($currentOptions);
    }

    private function isPermanentRedirectStatus(int $redirectStatus): bool
    {
        return ArrayUtils::contains($redirectStatus, [301, 308]);
    }

    public function ask(StyleInterface $io, array $currentOptions): int
    {
        return $io->ask(
            'How long (in seconds) do you want your redirects to be cached by visitors?',
            '30',
            ConfigOptionsValidator::validatePositiveNumber(...),
        );
    }

    public function getDependentOption(): string
    {
        return RedirectStatusCodeConfigOption::class;
    }
}
