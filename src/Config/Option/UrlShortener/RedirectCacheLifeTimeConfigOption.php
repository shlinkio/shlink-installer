<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\UrlShortener;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\DependentConfigOptionInterface;
use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidatorsTrait;
use Symfony\Component\Console\Style\StyleInterface;

class RedirectCacheLifeTimeConfigOption extends BaseConfigOption implements DependentConfigOptionInterface
{
    use ConfigOptionsValidatorsTrait;

    public function getEnvVar(): string
    {
        return 'REDIRECT_CACHE_LIFETIME';
    }

    public function shouldBeAsked(array $currentOptions): bool
    {
        $redirectStatus = $currentOptions[RedirectStatusCodeConfigOption::ENV_VAR] ?? null;
        return $redirectStatus === 301 && parent::shouldBeAsked($currentOptions);
    }

    public function ask(StyleInterface $io, array $currentOptions): int
    {
        return $io->ask(
            'How long (in seconds) do you want your redirects to be cached by visitors?',
            '30',
            [$this, 'validatePositiveNumber'],
        );
    }

    public function getDependentOption(): string
    {
        return RedirectStatusCodeConfigOption::class;
    }
}
